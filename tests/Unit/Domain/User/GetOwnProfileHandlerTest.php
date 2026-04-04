<?php

declare(strict_types=1);

use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetOwnProfileQuery;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Query\GetOwnProfileHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeUserRepository;

it('returns the user when found', function (): void {
    $user = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserName('John Doe'),
        new Email('john@example.com'),
    );

    $repository = new FakeUserRepository(['550e8400-e29b-41d4-a716-446655440000' => $user]);
    $handler = new GetOwnProfileHandler($repository);

    $result = $handler->handle(new GetOwnProfileQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($result->name->value)->toBe('John Doe')
        ->and($result->email->value)->toBe('john@example.com');
});

it('throws UserNotFoundException when user does not exist', function (): void {
    $handler = new GetOwnProfileHandler(new FakeUserRepository);

    $handler->handle(new GetOwnProfileQuery('550e8400-e29b-41d4-a716-446655440000'));
})->throws(UserNotFoundException::class);
