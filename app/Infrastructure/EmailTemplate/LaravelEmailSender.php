<?php

declare(strict_types=1);

namespace App\Infrastructure\EmailTemplate;

use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Mail\Message;

final readonly class LaravelEmailSender implements EmailSender
{
    private const string DYNAMIC_MAILER = 'tenant_dynamic';

    public function __construct(
        private MailFactory $mailFactory,
        private ConfigRepository $configRepository,
    ) {}

    public function sendHtml(MailTransport $mailTransport, string $recipientEmail, string $subject, string $htmlBody): void
    {
        if ($mailTransport->isCustom) {
            $this->configRepository->set('mail.mailers.'.self::DYNAMIC_MAILER, [
                'transport' => 'smtp',
                'host' => $mailTransport->host,
                'port' => $mailTransport->port,
                'username' => $mailTransport->username,
                'password' => $mailTransport->password,
                'encryption' => $mailTransport->encryption,
            ]);
        }

        $name = $mailTransport->isCustom ? self::DYNAMIC_MAILER : null;

        $this->mailFactory->mailer($name)->html($htmlBody, static function (Message $message) use ($mailTransport, $recipientEmail, $subject): void {
            $message->from($mailTransport->fromAddress, $mailTransport->fromName);
            $message->to($recipientEmail);
            $message->subject($subject);
        });
    }
}
