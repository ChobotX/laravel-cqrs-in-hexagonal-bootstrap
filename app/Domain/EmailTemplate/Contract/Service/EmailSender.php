<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Contract\Service;

use App\Domain\Tenancy\Contract\ValueObject\MailTransport;

/**
 * Domain service contract for email sender in the EmailTemplate bounded context.
 */
interface EmailSender
{
    /** Executes the side effect synchronously using the supplied transport. */
    public function sendHtml(MailTransport $mailTransport, string $recipientEmail, string $subject, string $htmlBody): void;
}
