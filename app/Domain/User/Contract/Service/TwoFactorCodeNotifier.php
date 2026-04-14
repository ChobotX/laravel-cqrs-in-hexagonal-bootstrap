<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface TwoFactorCodeNotifier
{
    public function send(string $email, string $subject, string $body): void;
}
