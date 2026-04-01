<?php

declare(strict_types=1);

use App\Domain\User\Contract\UserId;
use App\Domain\User\Email;
use App\Domain\User\User;
use App\Domain\User\UserName;

it('can be constructed with all properties', function (): void {
    $id = new UserId('550e8400-e29b-41d4-a716-446655440000');
    $email = new Email('john@example.com');
    $user = new User($id, new UserName('John Doe'), $email);

    expect($user->id)->toBe($id)
        ->and($user->name->value)->toBe('John Doe')
        ->and($user->email)->toBe($email);
});
