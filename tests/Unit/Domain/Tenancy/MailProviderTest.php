<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Enum\MailProvider;

it('returns SMTP presets for known providers', function (): void {
    expect(MailProvider::Mailpit->preset())->toBe(['host' => 'mailpit', 'port' => MailProvider::MAILPIT_PORT, 'encryption' => null])
        ->and(MailProvider::Mailjet->preset())->toBe(['host' => 'in-v3.mailjet.com', 'port' => MailProvider::SMTP_SUBMISSION_PORT, 'encryption' => 'tls'])
        ->and(MailProvider::Mailgun->preset())->toBe(['host' => 'smtp.mailgun.org', 'port' => MailProvider::SMTP_SUBMISSION_PORT, 'encryption' => 'tls']);
});

it('returns null preset for the custom provider', function (): void {
    expect(MailProvider::Custom->preset())->toBeNull();
});
