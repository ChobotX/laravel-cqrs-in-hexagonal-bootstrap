<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\EmailTemplate\Contract\Service\EmailSender;

final class FakeEmailSender implements EmailSender
{
    /** @var list<array{recipientEmail: string, subject: string, htmlBody: string}> */
    public array $sent = [];

    public function sendHtml(string $recipientEmail, string $subject, string $htmlBody): void
    {
        $this->sent[] = ['recipientEmail' => $recipientEmail, 'subject' => $subject, 'htmlBody' => $htmlBody];
    }
}
