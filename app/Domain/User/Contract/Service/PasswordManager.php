<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface PasswordManager
{
    public function setPassword(string $userId, string $rawPassword): void;
}
