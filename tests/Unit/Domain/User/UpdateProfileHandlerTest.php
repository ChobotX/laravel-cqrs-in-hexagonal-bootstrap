<?php

declare(strict_types=1);

use App\Contract\Auth\PasswordManager;
use App\Domain\User\Command\UpdateProfile\UpdateProfileCommand;
use App\Domain\User\Command\UpdateProfile\UpdateProfileHandler;
use App\Domain\User\Contract\Event\UserUpdated;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\Exception\InvalidUserDataException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserRepository;

/** @param list<array{userId: string, rawPassword: string}> $calls */
function profilePasswordManager(array &$calls = []): PasswordManager
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

it('updates name and keeps email unchanged', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateProfileHandler($repository, profilePasswordManager(), $eventCollector);

    $handler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
        rawPassword: null,
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->name->value)->toBe('Jane Doe')
        ->and($repository->saved[0]->email->value)->toBe('john@example.com');
});

it('sets password when provided', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    /** @var list<array{userId: string, rawPassword: string}> $calls */
    $calls = [];
    $handler = new UpdateProfileHandler($repository, profilePasswordManager($calls), $eventCollector);

    $handler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        rawPassword: 'new-password-123',
    ));

    expect($calls)->toHaveCount(1);
    expect($calls[0])->toHaveKey('rawPassword')
        ->and($calls[0]['rawPassword'])->toBe('new-password-123');
});

it('skips password when null', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    /** @var list<array{userId: string, rawPassword: string}> $calls */
    $calls = [];
    $handler = new UpdateProfileHandler($repository, profilePasswordManager($calls), $eventCollector);

    $handler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        rawPassword: null,
    ));

    expect($calls)->toBeEmpty();
});

it('skips password when empty string', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    /** @var list<array{userId: string, rawPassword: string}> $calls */
    $calls = [];
    $handler = new UpdateProfileHandler($repository, profilePasswordManager($calls), $eventCollector);

    $handler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        rawPassword: '',
    ));

    expect($calls)->toBeEmpty();
});

it('collects UserUpdated event', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateProfileHandler($repository, profilePasswordManager(), $eventCollector);

    $handler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
        rawPassword: null,
    ));

    expect($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(UserUpdated::class);
    assert($eventCollector->collected[0] instanceof UserUpdated);
    expect($eventCollector->collected[0]->name)->toBe('Jane Doe')
        ->and($eventCollector->collected[0]->email)->toBe('john@example.com');
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $repository = new FakeUserRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateProfileHandler($repository, profilePasswordManager(), $eventCollector);

    $handler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
        rawPassword: null,
    ));
})->throws(UserNotFoundException::class);

it('throws when name is empty', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateProfileHandler($repository, profilePasswordManager(), $eventCollector);

    $handler->handle(new UpdateProfileCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: '',
        rawPassword: null,
    ));
})->throws(InvalidUserDataException::class, 'User name must not be empty.');
