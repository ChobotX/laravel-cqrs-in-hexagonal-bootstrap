<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface PasswordResetBroker
{
    /**
     * Creates a password reset token and returns the reset URL.
     * Returns null if the user does not exist.
     */
    public function createResetLink(string $email): ?string;

    /**
     * Validates the token and resets the password.
     * Returns the user ID on success.
     *
     * @throws \App\Domain\User\Contract\Exception\InvalidPasswordResetTokenException
     */
    public function reset(string $email, string $token, string $newPassword): string;
}
