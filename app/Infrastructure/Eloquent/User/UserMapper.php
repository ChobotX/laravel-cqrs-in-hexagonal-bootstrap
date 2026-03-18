<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Domain\User\Email;
use App\Domain\User\User;
use App\Domain\User\UserId;

final readonly class UserMapper
{
    public function toDomain(UserModel $userModel): User
    {
        return new User(
            id: new UserId($userModel->id),
            name: $userModel->name,
            email: new Email($userModel->email),
        );
    }
}
