<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\AcceptInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\UserInviteAccepted;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\AcceptInviteHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserRepository;

/**
 * @param  list<array{userId: string, rawPassword: string}>  $calls
 */
function acceptFakePasswordManager(array &$calls = []): PasswordManager
{
    return new class($calls) implements PasswordManager
    {
        /** @param list<array{userId: string, rawPassword: string}> $calls */
        public function __construct(public array &$calls) {}

        public function setPassword(string $userId, string $rawPassword): void
        {
            $this->calls[] = ['userId' => $userId, 'rawPassword' => $rawPassword];
        }
    };
}

it('sets the password for a non-activated user', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $passwordCalls = [];

    $handler = new AcceptInviteHandler(
        $repository,
        acceptFakePasswordManager($passwordCalls),
        new FakeEventCollector,
    );

    $handler->handle(new AcceptInviteCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        rawPassword: 'SecureP@ssw0rd!',
    ));

    expect($passwordCalls)->toHaveCount(1)
        ->and($passwordCalls[0]['userId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($passwordCalls[0]['rawPassword'])->toBe('SecureP@ssw0rd!');
});

it('collects UserInviteAccepted and PasswordChanged events', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $eventCollector = new FakeEventCollector;
    $passwordCalls = [];

    $handler = new AcceptInviteHandler(
        $repository,
        acceptFakePasswordManager($passwordCalls),
        $eventCollector,
    );

    $handler->handle(new AcceptInviteCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        rawPassword: 'SecureP@ssw0rd!',
    ));

    expect($eventCollector->collected)->toHaveCount(2);

    expect($eventCollector->collected[0])->toBeInstanceOf(UserInviteAccepted::class);
    assert($eventCollector->collected[0] instanceof UserInviteAccepted);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);

    expect($eventCollector->collected[1])->toBeInstanceOf(PasswordChanged::class);
    assert($eventCollector->collected[1] instanceof PasswordChanged);
    expect($eventCollector->collected[1]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[1]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $passwordCalls = [];

    $handler = new AcceptInviteHandler(
        new FakeUserRepository,
        acceptFakePasswordManager($passwordCalls),
        new FakeEventCollector,
    );

    $handler->handle(new AcceptInviteCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        rawPassword: 'SecureP@ssw0rd!',
    ));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');

it('throws UserAlreadyActivatedException when user is activated', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        isActivated: true,
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $passwordCalls = [];

    $handler = new AcceptInviteHandler(
        $repository,
        acceptFakePasswordManager($passwordCalls),
        new FakeEventCollector,
    );

    $handler->handle(new AcceptInviteCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        rawPassword: 'SecureP@ssw0rd!',
    ));
})->throws(UserAlreadyActivatedException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] has already been activated.');

it('does not set password when user is not found', function (): void {
    $passwordCalls = [];
    $eventCollector = new FakeEventCollector;

    $handler = new AcceptInviteHandler(
        new FakeUserRepository,
        acceptFakePasswordManager($passwordCalls),
        $eventCollector,
    );

    try {
        $handler->handle(new AcceptInviteCommand(
            userId: '550e8400-e29b-41d4-a716-446655440000',
            rawPassword: 'SecureP@ssw0rd!',
        ));
    } catch (UserNotFoundException) {
    }

    expect($passwordCalls)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('does not set password when user is already activated', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        isActivated: true,
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $passwordCalls = [];
    $eventCollector = new FakeEventCollector;

    $handler = new AcceptInviteHandler(
        $repository,
        acceptFakePasswordManager($passwordCalls),
        $eventCollector,
    );

    try {
        $handler->handle(new AcceptInviteCommand(
            userId: '550e8400-e29b-41d4-a716-446655440000',
            rawPassword: 'SecureP@ssw0rd!',
        ));
    } catch (UserAlreadyActivatedException) {
    }

    expect($passwordCalls)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});
