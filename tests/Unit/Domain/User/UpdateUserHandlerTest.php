<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Domain\User\Constant\UserFields;
use App\Domain\User\Contract\Command\UpdateUserCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserUpdated;
use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;
use App\Domain\User\Contract\Exception\InvalidUserDataException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\UpdateUserHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserRepository;

it('saves an updated user via the repository', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
        email: 'jane@example.com',
    ));

    expect($repository->saved)->toHaveCount(1);
    expect($repository->saved[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($repository->saved[0]->name->value)->toBe('Jane Doe')
        ->and($repository->saved[0]->email->value)->toBe('jane@example.com');
});

it('collects a UserUpdated event with changes', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
        email: 'jane@example.com',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(UserUpdated::class);
    assert($eventCollector->collected[0] instanceof UserUpdated);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->changes())->toEqual([
            new PropertyChange(UserFields::NAME, 'John Doe', 'Jane Doe'),
            new PropertyChange(UserFields::EMAIL, 'john@example.com', 'jane@example.com'),
        ])
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('does not save or collect event when data is unchanged', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
    ));

    expect($repository->saved)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('saves an updated user with avatarFileId', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
        avatarFileId: '770e8400-e29b-41d4-a716-446655440000',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->avatarFileId?->value)->toBe('770e8400-e29b-41d4-a716-446655440000');

    $event = $eventCollector->collected[0];
    assert($event instanceof UserUpdated);
    $avatarChange = array_values(array_filter($event->changes(), static fn (PropertyChange $propertyChange): bool => $propertyChange->property === UserFields::AVATAR_FILE_ID));
    expect($avatarChange)->toHaveCount(1)
        ->and($avatarChange[0]->old)->toBeNull()
        ->and($avatarChange[0]->new)->toBe('770e8400-e29b-41d4-a716-446655440000');
});

it('clears avatarFileId when set to null', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
        avatarFileId: new App\Domain\File\Contract\ValueObject\FileId('770e8400-e29b-41d4-a716-446655440000'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
    ));

    expect($repository->saved[0]->avatarFileId)->toBeNull();

    $event = $eventCollector->collected[0];
    assert($event instanceof UserUpdated);
    $avatarChange = array_values(array_filter($event->changes(), static fn (PropertyChange $propertyChange): bool => $propertyChange->property === UserFields::AVATAR_FILE_ID));
    expect($avatarChange)->toHaveCount(1)
        ->and($avatarChange[0]->old)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($avatarChange[0]->new)->toBeNull();
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $repository = new FakeUserRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Doe',
        email: 'jane@example.com',
    ));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');

it('throws when name is empty', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: '',
        email: 'jane@example.com',
    ));
})->throws(InvalidUserDataException::class, 'User name must not be empty.');

it('throws when email belongs to another user', function (): void {
    $user1 = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );
    $user2 = new User(
        new UserId('660e8400-e29b-41d4-a716-446655440000'),
        new UserName('Jane Doe'),
        new Email('jane@example.com'),
    );

    $repository = new FakeUserRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $user1,
        '660e8400-e29b-41d4-a716-446655440000' => $user2,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'jane@example.com',
    ));
})->throws(EmailAlreadyExistsException::class, 'A user with email [jane@example.com] already exists.');

it('throws when email matches another user case-insensitively', function (): void {
    $user1 = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );
    $user2 = new User(
        new UserId('660e8400-e29b-41d4-a716-446655440000'),
        new UserName('Jane Doe'),
        new Email('jane@example.com'),
    );

    $repository = new FakeUserRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $user1,
        '660e8400-e29b-41d4-a716-446655440000' => $user2,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'Jane@Example.COM',
    ));
})->throws(EmailAlreadyExistsException::class);

it('allows keeping the same email for the same user', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateUserHandler($repository, $eventCollector);

    $handler->handle(new UpdateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Updated',
        email: 'john@example.com',
    ));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->name->value)->toBe('John Updated');
});
