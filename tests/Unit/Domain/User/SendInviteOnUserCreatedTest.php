<?php

declare(strict_types=1);

use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Domain\User\Contract\Command\SendUserInviteCommand;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\EventHandler\SendInviteOnUserCreated;
use Tests\Helper\FakeCommandBus;

it('dispatches SendUserInviteCommand on user creation', function (): void {
    $commandBus = new FakeCommandBus;

    $handler = new SendInviteOnUserCreated($commandBus);

    $handler->handle(new UserCreated(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($commandBus->dispatched)->toHaveCount(1);
    expect($commandBus->dispatched[0])->toBeInstanceOf(SendUserInviteCommand::class);
    assert($commandBus->dispatched[0] instanceof SendUserInviteCommand);
    expect($commandBus->dispatched[0]->userId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('passes the correct userId from the event', function (): void {
    $commandBus = new FakeCommandBus;

    $handler = new SendInviteOnUserCreated($commandBus);

    $handler->handle(new UserCreated(
        userId: '660e8400-e29b-41d4-a716-446655440000',
        name: 'Jane Smith',
        email: 'jane@example.com',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($commandBus->dispatched)->toHaveCount(1);
    assert($commandBus->dispatched[0] instanceof SendUserInviteCommand);
    expect($commandBus->dispatched[0]->userId)->toBe('660e8400-e29b-41d4-a716-446655440000');
});

it('silently skips when invite dispatch reports missing user', function (): void {
    $handler = new SendInviteOnUserCreated(new class implements CommandBus
    {
        public function dispatch(Command $command): void
        {
            throw new UserNotFoundException('550e8400-e29b-41d4-a716-446655440000');
        }
    });

    $handler->handle(new UserCreated(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Deleted User',
        email: 'deleted@example.com',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect(true)->toBeTrue();
});
