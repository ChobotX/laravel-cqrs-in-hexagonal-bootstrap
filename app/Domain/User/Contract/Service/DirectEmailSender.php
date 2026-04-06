<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface DirectEmailSender
{
    public function sendToUser(string $userId, string $subject, string $body): void;
}
