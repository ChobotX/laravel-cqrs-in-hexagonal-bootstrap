<?php

declare(strict_types=1);

namespace App\Infrastructure\EmailTemplate;

use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;

final readonly class LaravelEmailSender implements EmailSender
{
    public function __construct(
        private Mailer $mailer,
    ) {}

    public function sendHtml(string $recipientEmail, string $subject, string $htmlBody): void
    {
        $this->mailer->html($htmlBody, static function (Message $message) use ($recipientEmail, $subject): void {
            $message->to($recipientEmail);
            $message->subject($subject);
        });
    }
}
