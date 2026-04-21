<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\BuildSsoRedirectInstructionQuery;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Handler\Query\BuildSsoRedirectInstructionHandler;
use Tests\Helper\FakeSsoAuthenticator;
use Tests\Helper\FakeSsoAuthenticatorRegistry;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\FakeSsoLoginSession;
use Tests\Helper\SsoFixtures;

it('returns the IdP redirect and stores the handshake', function (): void {
    $configuration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $authenticator = new FakeSsoAuthenticator(nextRedirect: new RedirectInstruction(
        url: 'https://idp.example.com/auth',
        stateToStore: 'state-xyz',
        nonceToStore: 'nonce-xyz',
    ));
    $session = new FakeSsoLoginSession;

    $redirectInstruction = new BuildSsoRedirectInstructionHandler($repository, new FakeSsoAuthenticatorRegistry($authenticator), $session)
        ->handle(new BuildSsoRedirectInstructionQuery($configuration->slug));

    expect($redirectInstruction->url)->toBe('https://idp.example.com/auth')
        ->and($session->handshakes[$configuration->slug] ?? null)->toBe(['state' => 'state-xyz', 'nonce' => 'nonce-xyz']);
});

it('does not store a handshake when the authenticator returns none', function (): void {
    $configuration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $authenticator = new FakeSsoAuthenticator(nextRedirect: new RedirectInstruction(url: 'https://idp.example.com/sso', usesPostBinding: true));
    $session = new FakeSsoLoginSession;

    new BuildSsoRedirectInstructionHandler($repository, new FakeSsoAuthenticatorRegistry($authenticator), $session)
        ->handle(new BuildSsoRedirectInstructionQuery($configuration->slug));

    expect($session->handshakes)->toBe([]);
});

it('rejects an unknown slug', function (): void {
    new BuildSsoRedirectInstructionHandler(new FakeSsoConfigurationRepository, new FakeSsoAuthenticatorRegistry, new FakeSsoLoginSession)
        ->handle(new BuildSsoRedirectInstructionQuery('missing'));
})->throws(SsoConfigurationNotFoundException::class);
