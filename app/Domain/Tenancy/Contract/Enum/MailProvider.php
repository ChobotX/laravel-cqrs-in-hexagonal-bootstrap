<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Enum;

/**
 * Known SMTP mail providers tenants can pick. `Custom` allows arbitrary host/port without a preset.
 */
enum MailProvider: string
{
    case Mailpit = 'mailpit';

    case Mailjet = 'mailjet';

    case Mailgun = 'mailgun';

    case Custom = 'custom';

    public const int MAILPIT_PORT = 1025;

    public const int SMTP_SUBMISSION_PORT = 587;

    /**
     * @return array{host: string, port: int, encryption: ?string}|null
     */
    public function preset(): ?array
    {
        return match ($this) {
            self::Mailpit => ['host' => 'mailpit', 'port' => self::MAILPIT_PORT, 'encryption' => null],
            self::Mailjet => ['host' => 'in-v3.mailjet.com', 'port' => self::SMTP_SUBMISSION_PORT, 'encryption' => 'tls'],
            self::Mailgun => ['host' => 'smtp.mailgun.org', 'port' => self::SMTP_SUBMISSION_PORT, 'encryption' => 'tls'],
            self::Custom => null,
        };
    }
}
