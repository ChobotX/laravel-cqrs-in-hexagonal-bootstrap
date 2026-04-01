<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;

it('counts users', function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c00', 'name' => 'Alice', 'email' => 'alice@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c01', 'name' => 'Bob', 'email' => 'bob@example.com']);

    $userRepository = app(App\Domain\User\Contract\UserRepository::class);

    expect($userRepository->count())->toBe(2);
});

it('returns zero count when no users exist', function (): void {
    $userRepository = app(App\Domain\User\Contract\UserRepository::class);

    expect($userRepository->count())->toBe(0);
});
