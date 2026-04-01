<?php

declare(strict_types=1);

use App\Domain\User\Command\DeleteUser\DeleteUserCommand;
use App\Domain\User\Command\DeleteUser\DeleteUserHandler;
use App\Domain\User\Email;
use App\Domain\User\Event\UserDeleted;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserRepository;

it('calls delete on the repository', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteUserHandler($repository, $eventCollector);

    $handler->handle(new DeleteUserCommand(id: '550e8400-e29b-41d4-a716-446655440000'));

    expect($repository->deleted)->toHaveCount(1)
        ->and($repository->deleted[0])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('collects an enriched UserDeleted event', function (): void {
    $existing = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteUserHandler($repository, $eventCollector);

    $handler->handle(new DeleteUserCommand(id: '550e8400-e29b-41d4-a716-446655440000'));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(UserDeleted::class);
    assert($eventCollector->collected[0] instanceof UserDeleted);
    expect($eventCollector->collected[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $repository = new FakeUserRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteUserHandler($repository, $eventCollector);

    $handler->handle(new DeleteUserCommand(id: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');
