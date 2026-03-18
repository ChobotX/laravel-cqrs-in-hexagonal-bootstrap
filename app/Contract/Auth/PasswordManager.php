<?php

declare(strict_types=1);

namespace App\Contract\Auth;

interface PasswordManager
{
    public function setPassword(string $userId, string $rawPassword): void;
}
