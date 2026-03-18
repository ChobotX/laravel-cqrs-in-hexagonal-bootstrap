<?php

declare(strict_types=1);

use App\Domain\User\Email;
use App\Domain\User\Query\ListUsers\ListUsersHandler;
use App\Domain\User\Query\ListUsers\ListUsersQuery;
use App\Domain\User\User;
use App\Domain\User\UserId;
use Tests\Helper\FakeUserRepository;

it('returns all users from the repository', function (): void {
    $users = [
        '550e8400-e29b-41d4-a716-446655440000' => new User(new UserId('550e8400-e29b-41d4-a716-446655440000'), 'John Doe', new Email('john@example.com')),
        '660e8400-e29b-41d4-a716-446655440000' => new User(new UserId('660e8400-e29b-41d4-a716-446655440000'), 'Jane Doe', new Email('jane@example.com')),
    ];

    $repository = new FakeUserRepository($users);

    $handler = new ListUsersHandler($repository);

    $result = $handler->handle(new ListUsersQuery);

    expect($result)->toHaveCount(2)
        ->and($result[0]->name)->toBe('John Doe')
        ->and($result[1]->name)->toBe('Jane Doe');
});

it('returns an empty list when no users exist', function (): void {
    $repository = new FakeUserRepository;

    $handler = new ListUsersHandler($repository);

    $result = $handler->handle(new ListUsersQuery);

    expect($result)->toBeEmpty();
});
