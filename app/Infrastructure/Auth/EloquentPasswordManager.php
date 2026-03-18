<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Contract\Auth\PasswordManager;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

final readonly class EloquentPasswordManager implements PasswordManager
{
    public function setPassword(string $userId, string $rawPassword): void
    {
        UserModel::where('id', $userId)->update([
            'password' => Hash::make($rawPassword),
        ]);
    }
}
