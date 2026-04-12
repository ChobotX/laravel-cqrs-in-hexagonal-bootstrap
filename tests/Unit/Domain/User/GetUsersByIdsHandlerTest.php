<?php

declare(strict_types=1);

use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUsersByIdsQuery;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Handler\Query\GetUsersByIdsHandler;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use Tests\Helper\FakeUserRepository;

it('returns empty list when userIds is empty', function (): void {
    $repository = new FakeUserRepository;

    $handler = new GetUsersByIdsHandler($repository);

    $result = $handler->handle(new GetUsersByIdsQuery([]));

    expect($result)->toBe([]);
});

it('returns users matching the given ids', function (): void {
    $user1 = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440001'),
        new UserName('Alice Smith'),
        new Email('alice@example.com'),
    );
    $user2 = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440002'),
        new UserName('Bob Jones'),
        new Email('bob@example.com'),
    );

    $repository = new FakeUserRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $user1,
        '550e8400-e29b-41d4-a716-446655440002' => $user2,
    ]);

    $handler = new GetUsersByIdsHandler($repository);

    $result = $handler->handle(new GetUsersByIdsQuery(['550e8400-e29b-41d4-a716-446655440001']));

    expect($result)->toHaveCount(1)
        ->and($result[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440001');
});

it('returns all users when all ids are provided', function (): void {
    $user1 = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440001'),
        new UserName('Alice Smith'),
        new Email('alice@example.com'),
    );
    $user2 = new User(
        new UserId('550e8400-e29b-41d4-a716-446655440002'),
        new UserName('Bob Jones'),
        new Email('bob@example.com'),
    );

    $repository = new FakeUserRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $user1,
        '550e8400-e29b-41d4-a716-446655440002' => $user2,
    ]);

    $handler = new GetUsersByIdsHandler($repository);

    $result = $handler->handle(new GetUsersByIdsQuery([
        '550e8400-e29b-41d4-a716-446655440001',
        '550e8400-e29b-41d4-a716-446655440002',
    ]));

    expect($result)->toHaveCount(2);
});
