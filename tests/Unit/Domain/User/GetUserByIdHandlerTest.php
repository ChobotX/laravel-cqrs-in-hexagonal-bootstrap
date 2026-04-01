<?php

declare(strict_types=1);

use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Query\GetUserById\GetUserByIdHandler;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Domain\User\User;
use App\Domain\User\UserName;
use Tests\Helper\FakeUserRepository;

it('returns user data when user exists', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);

    $handler = new GetUserByIdHandler($repository);

    $result = $handler->handle(new GetUserByIdQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($result->name->value)->toBe('John Doe')
        ->and($result->email->value)->toBe('john@example.com');
});

it('throws EntityNotFoundException when user does not exist', function (): void {
    $repository = new FakeUserRepository;

    $handler = new GetUserByIdHandler($repository);

    $handler->handle(new GetUserByIdQuery('550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class, 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.');
