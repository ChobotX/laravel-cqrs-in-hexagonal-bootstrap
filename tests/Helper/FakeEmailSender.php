<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;

final class FakeEmailSender implements EmailSender
{
    /** @var list<array{transport: MailTransport, recipientEmail: string, subject: string, htmlBody: string}> */
    public array $sent = [];

    public function sendHtml(MailTransport $mailTransport, string $recipientEmail, string $subject, string $htmlBody): void
    {
        $this->sent[] = [
            'transport' => $mailTransport,
            'recipientEmail' => $recipientEmail,
            'subject' => $subject,
            'htmlBody' => $htmlBody,
        ];
    }
}
