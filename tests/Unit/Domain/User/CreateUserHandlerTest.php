<?php

declare(strict_types=1);

use App\Domain\User\Command\CreateUser\CreateUserCommand;
use App\Domain\User\Command\CreateUser\CreateUserHandler;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;
use App\Domain\User\Contract\Exception\InvalidUserDataException;
use App\Domain\User\Contract\User;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserRepository;

it('saves a user via the repository', function (): void {
    $repository = new FakeUserRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateUserHandler($repository, $eventCollector);

    $handler->handle(new CreateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
    ));

    expect($repository->saved)->toHaveCount(1);
    expect($repository->saved[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($repository->saved[0]->name->value)->toBe('John Doe')
        ->and($repository->saved[0]->email->value)->toBe('john@example.com');
});

it('collects an enriched UserCreated event', function (): void {
    $repository = new FakeUserRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateUserHandler($repository, $eventCollector);

    $handler->handle(new CreateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(UserCreated::class);
    assert($eventCollector->collected[0] instanceof UserCreated);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->name)->toBe('John Doe')
        ->and($eventCollector->collected[0]->email)->toBe('john@example.com')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws when name is empty', function (): void {
    $repository = new FakeUserRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateUserHandler($repository, $eventCollector);

    $handler->handle(new CreateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: '   ',
        email: 'john@example.com',
    ));
})->throws(InvalidUserDataException::class, 'User name must not be empty.');

it('throws when email already exists', function (): void {
    $existing = new User(
        new UserId('660e8400-e29b-41d4-a716-446655440000'),
        new UserName('Existing User'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['660e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateUserHandler($repository, $eventCollector);

    $handler->handle(new CreateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
    ));
})->throws(EmailAlreadyExistsException::class, 'A user with email [john@example.com] already exists.');
