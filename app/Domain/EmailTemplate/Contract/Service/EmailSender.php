<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

/**
 * Domain service contract for email sender in the EmailTemplate bounded context.
 */
interface EmailSender
{
    /** Executes the side effect synchronously. */
    public function sendHtml(string $recipientEmail, string $subject, string $htmlBody): void;
}
