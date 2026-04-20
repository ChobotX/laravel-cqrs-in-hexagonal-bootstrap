<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\MarkUserActivatedCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\UserInviteAccepted;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Command\MarkUserActivatedHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserRepository;

it('activates a pending user and emits UserInviteAccepted', function (): void {
    $user = new User(
        id: new UserId('550e8400-e29b-41d4-a716-446655440000'),
        name: new UserName('Pending'),
        email: new Email('pending@example.com'),
        isActivated: false,
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $events = new FakeEventCollector;
    $handler = new MarkUserActivatedHandler($repository, $events);

    $handler->handle(new MarkUserActivatedCommand(userId: $user->id->value));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->isActivated)->toBeTrue()
        ->and($events->collected[0])->toBeInstanceOf(UserInviteAccepted::class);
});

it('throws when the user is missing', function (): void {
    $handler = new MarkUserActivatedHandler(new FakeUserRepository, new FakeEventCollector);

    $handler->handle(new MarkUserActivatedCommand(userId: '550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class);

it('throws when the user is already activated', function (): void {
    $user = new User(
        id: new UserId('550e8400-e29b-41d4-a716-446655440000'),
        name: new UserName('Done'),
        email: new Email('done@example.com'),
        isActivated: true,
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $handler = new MarkUserActivatedHandler($repository, new FakeEventCollector);

    $handler->handle(new MarkUserActivatedCommand(userId: $user->id->value));
})->throws(UserAlreadyActivatedException::class);
