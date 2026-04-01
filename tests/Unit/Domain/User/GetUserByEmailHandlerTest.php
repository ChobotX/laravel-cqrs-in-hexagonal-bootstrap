<?php

declare(strict_types=1);

use App\Domain\User\Email;
use App\Domain\User\Query\GetUserByEmail\GetUserByEmailHandler;
use App\Domain\User\Query\GetUserByEmail\GetUserByEmailQuery;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserName;
use Tests\Helper\FakeUserRepository;

it('returns user when found by email', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);

    $handler = new GetUserByEmailHandler($repository);

    $result = $handler->handle(new GetUserByEmailQuery('john@example.com'));

    expect($result)->toBeInstanceOf(User::class);
    assert($result instanceof User);
    expect($result->email->value)->toBe('john@example.com');
});

it('returns null when user not found', function (): void {
    $repository = new FakeUserRepository;

    $handler = new GetUserByEmailHandler($repository);

    $result = $handler->handle(new GetUserByEmailQuery('nobody@example.com'));

    expect($result)->toBeNull();
});
