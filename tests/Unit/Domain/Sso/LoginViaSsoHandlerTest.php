<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Command\LinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Command\LoginViaSsoCommand;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Event\SsoLoginFailed;
use App\Domain\Sso\Contract\Event\SsoLoginSucceeded;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use App\Domain\Sso\Handler\Command\LoginViaSsoHandler;
use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Domain\User\Contract\Command\MarkUserActivatedCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeSsoAuthenticator;
use Tests\Helper\FakeSsoAuthenticatorRegistry;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\FakeUserRepository;
use Tests\Helper\FakeUserSsoIdentityRepository;
use Tests\Helper\SsoFixtures;
use Tests\Helper\StagedUserRepository;

$ssoUser = fn (bool $activated = true, string $email = 'user@example.com'): User => new User(
    id: new UserId(SsoFixtures::USER_ID),
    name: new UserName('Existing'),
    email: new Email($email),
    isActivated: $activated,
);

$loginCommand = fn (): LoginViaSsoCommand => new LoginViaSsoCommand(
    configurationId: SsoFixtures::CONFIG_ID,
    callbackPayload: ['code' => 'abc'],
    newUserIdIfProvisioned: '99999999-9999-9999-9999-999999999999',
    newIdentityId: SsoFixtures::IDENTITY_ID,
);

$buildHandler = fn (
    FakeSsoConfigurationRepository $fakeSsoConfigurationRepository,
    FakeUserSsoIdentityRepository $fakeUserSsoIdentityRepository,
    UserRepository $userRepository,
    FakeSsoAuthenticator $fakeSsoAuthenticator,
    FakeCommandBus $fakeCommandBus,
    FakeEventCollector $fakeEventCollector,
): LoginViaSsoHandler => new LoginViaSsoHandler(
    $fakeSsoConfigurationRepository,
    $fakeUserSsoIdentityRepository,
    $userRepository,
    new FakeSsoAuthenticatorRegistry($fakeSsoAuthenticator),
    $fakeCommandBus,
    $fakeEventCollector,
);

it('logs in an already-linked identity without provisioning', function () use ($ssoUser, $loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration();
    $identity = SsoFixtures::identity();
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $identityRepo = new FakeUserSsoIdentityRepository([$identity->id->value => $identity]);
    $userRepo = new FakeUserRepository([SsoFixtures::USER_ID => $ssoUser()]);
    $bus = new FakeCommandBus;
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, $identityRepo, $userRepo, new FakeSsoAuthenticator, $bus, $events);

    $loginViaSsoHandler->handle($loginCommand());

    expect($bus->dispatched)->toBeEmpty()
        ->and($events->collected[0])->toBeInstanceOf(SsoLoginSucceeded::class);
});

it('rejects a missing configuration', function () use ($loginCommand, $buildHandler): void {
    $loginViaSsoHandler = $buildHandler(
        new FakeSsoConfigurationRepository,
        new FakeUserSsoIdentityRepository,
        new FakeUserRepository,
        new FakeSsoAuthenticator,
        new FakeCommandBus,
        new FakeEventCollector,
    );

    $loginViaSsoHandler->handle($loginCommand());
})->throws(SsoConfigurationNotFoundException::class);

it('rejects a disabled configuration and records failure', function () use ($loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(enabled: false);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, new FakeUserRepository, new FakeSsoAuthenticator, new FakeCommandBus, $events);

    expect(fn () => $loginViaSsoHandler->handle($loginCommand()))
        ->toThrow(SsoLoginRejectedException::class);

    expect($events->collected[0])->toBeInstanceOf(SsoLoginFailed::class);
});

it('records authenticator errors as a failure event', function () use ($loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration();
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $authenticator = new FakeSsoAuthenticator(throwOnComplete: true);
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, new FakeUserRepository, $authenticator, new FakeCommandBus, $events);

    expect(fn () => $loginViaSsoHandler->handle($loginCommand()))
        ->toThrow(SsoLoginRejectedException::class);

    expect($events->collected[0])->toBeInstanceOf(SsoLoginFailed::class);
});

it('rejects linked-only when no link exists', function () use ($loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::LinkedOnly);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, new FakeUserRepository, new FakeSsoAuthenticator, new FakeCommandBus, $events);

    expect(fn () => $loginViaSsoHandler->handle($loginCommand()))
        ->toThrow(SsoLoginRejectedException::class);

    expect($events->collected[0])->toBeInstanceOf(SsoLoginFailed::class);
});

it('activates an invited user via JIT', function () use ($ssoUser, $loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::InvitedOnly);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $userRepo = new FakeUserRepository([SsoFixtures::USER_ID => $ssoUser(activated: false)]);
    $bus = new FakeCommandBus;
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, $userRepo, new FakeSsoAuthenticator, $bus, $events);

    $loginViaSsoHandler->handle($loginCommand());

    expect($bus->dispatched[0])->toBeInstanceOf(MarkUserActivatedCommand::class)
        ->and($bus->dispatched[1])->toBeInstanceOf(LinkSsoIdentityCommand::class);
});

it('rejects invited-only when no user exists with that email', function () use ($loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::InvitedOnly);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, new FakeUserRepository, new FakeSsoAuthenticator, new FakeCommandBus, $events);

    expect(fn () => $loginViaSsoHandler->handle($loginCommand()))
        ->toThrow(SsoLoginRejectedException::class);

    expect($events->collected[0])->toBeInstanceOf(SsoLoginFailed::class);
});

it('auto-creates an unknown user when JIT mode permits and domain matches', function () use ($ssoUser, $loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::AutoCreate, allowedEmailDomains: ['example.com']);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $userRepo = new StagedUserRepository(provisionedUser: $ssoUser(activated: false));
    $bus = new FakeCommandBus;
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, $userRepo, new FakeSsoAuthenticator, $bus, $events);

    $loginViaSsoHandler->handle($loginCommand());

    expect($bus->dispatched[0])->toBeInstanceOf(CreateUserCommand::class)
        ->and($bus->dispatched[1])->toBeInstanceOf(MarkUserActivatedCommand::class)
        ->and($bus->dispatched[2])->toBeInstanceOf(LinkSsoIdentityCommand::class)
        ->and($events->collected[0])->toBeInstanceOf(SsoLoginSucceeded::class);
});

it('returns existing user for auto-create when one already exists', function () use ($ssoUser, $loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::AutoCreate);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $userRepo = new FakeUserRepository([SsoFixtures::USER_ID => $ssoUser(activated: false)]);
    $bus = new FakeCommandBus;
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, $userRepo, new FakeSsoAuthenticator, $bus, $events);

    $loginViaSsoHandler->handle($loginCommand());

    expect($bus->dispatched[0])->toBeInstanceOf(MarkUserActivatedCommand::class)
        ->and($bus->dispatched[1])->toBeInstanceOf(LinkSsoIdentityCommand::class);
});

it('rejects auto-create when the email domain is not allowed', function () use ($loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::AutoCreate, allowedEmailDomains: ['acme.com']);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, new FakeUserRepository, new FakeSsoAuthenticator, new FakeCommandBus, $events);

    expect(fn () => $loginViaSsoHandler->handle($loginCommand()))
        ->toThrow(SsoLoginRejectedException::class);

    expect($events->collected[0])->toBeInstanceOf(SsoLoginFailed::class);
});

it('rejects auto-create when CreateUserCommand fails to materialise the user', function () use ($loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::AutoCreate, allowedEmailDomains: ['example.com']);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $events = new FakeEventCollector;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, new FakeUserRepository, new FakeSsoAuthenticator, new FakeCommandBus, $events);

    expect(fn () => $loginViaSsoHandler->handle($loginCommand()))
        ->toThrow(SsoLoginRejectedException::class);

    expect($events->collected[0])->toBeInstanceOf(SsoLoginFailed::class);
});

it('passes the asserted name through into provisioning', function () use ($ssoUser, $loginCommand, $buildHandler): void {
    $configuration = SsoFixtures::configuration(jitMode: JitMode::AutoCreate, allowedEmailDomains: ['example.com']);
    $configRepo = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $authenticator = new FakeSsoAuthenticator(nextIdentity: new SsoIdentity('subject-1', 'user@example.com', 'Asserted Name'));
    $userRepo = new StagedUserRepository(provisionedUser: $ssoUser());
    $bus = new FakeCommandBus;
    $loginViaSsoHandler = $buildHandler($configRepo, new FakeUserSsoIdentityRepository, $userRepo, $authenticator, $bus, new FakeEventCollector);

    $loginViaSsoHandler->handle($loginCommand());

    $createCommand = $bus->dispatched[0];
    expect($createCommand)->toBeInstanceOf(CreateUserCommand::class);
    assert($createCommand instanceof CreateUserCommand);
    expect($createCommand->name)->toBe('Asserted Name');
});
