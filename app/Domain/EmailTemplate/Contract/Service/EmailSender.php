<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

interface EmailSender
{
    public function sendHtml(string $recipientEmail, string $subject, string $htmlBody): void;
}
