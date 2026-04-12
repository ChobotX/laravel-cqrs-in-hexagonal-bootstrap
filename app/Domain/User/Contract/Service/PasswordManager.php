<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

/**
 * Domain service contract for password manager in the User bounded context.
 */
interface PasswordManager
{
    /** Contract operation `setPassword`; see infrastructure for behavior. */
    public function setPassword(string $userId, string $rawPassword): void;
}
