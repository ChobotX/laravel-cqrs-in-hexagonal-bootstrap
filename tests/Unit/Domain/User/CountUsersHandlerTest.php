<?php

declare(strict_types=1);

use App\Domain\User\Contract\Query\CountUsers\CountUsersQuery;
use App\Domain\User\Contract\User;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\Query\CountUsers\CountUsersHandler;
use App\Domain\User\UserName;
use Tests\Helper\FakeUserRepository;

it('returns the user count from the repository', function (): void {
    $users = [
        '550e8400-e29b-41d4-a716-446655440000' => new User(new UserId('550e8400-e29b-41d4-a716-446655440000'), new UserName('John Doe'), new Email('john@example.com')),
        '660e8400-e29b-41d4-a716-446655440000' => new User(new UserId('660e8400-e29b-41d4-a716-446655440000'), new UserName('Jane Doe'), new Email('jane@example.com')),
    ];

    $handler = new CountUsersHandler(new FakeUserRepository($users));

    expect($handler->handle(new CountUsersQuery))->toBe(2);
});

it('returns zero when no users exist', function (): void {
    $handler = new CountUsersHandler(new FakeUserRepository);

    expect($handler->handle(new CountUsersQuery))->toBe(0);
});
