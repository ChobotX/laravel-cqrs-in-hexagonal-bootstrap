<?php

declare(strict_types=1);

use App\Domain\EmailTemplate\Contract\Service\EmailSender;
use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use App\Infrastructure\EmailTemplate\LaravelEmailSender;
use Illuminate\Config\Repository as ConfigRepository;
use Tests\Helper\FakeMailer;
use Tests\Helper\FakeMailFactory;

it('sends via the default mailer when the transport is not custom', function (): void {
    $factory = new FakeMailFactory(new FakeMailer);
    $config = new ConfigRepository;

    $emailSender = new LaravelEmailSender($factory, $config);

    $emailSender->sendHtml(new MailTransport(
        provider: MailProvider::Custom,
        host: 'mailpit',
        port: MailProvider::MAILPIT_PORT,
        username: null,
        password: null,
        encryption: null,
        fromAddress: 'no-reply@platform.dev',
        fromName: 'Platform',
        isCustom: false,
    ), 'recipient@acme.com', 'Hello', '<p>Welcome</p>');

    expect($factory->resolvedNames)->toBe([null])
        ->and($factory->mailer->sent)->toHaveCount(1)
        ->and($factory->mailer->sent[0]['to'])->toBe('recipient@acme.com')
        ->and($factory->mailer->sent[0]['subject'])->toBe('Hello')
        ->and($factory->mailer->sent[0]['body'])->toBe('<p>Welcome</p>');
});

it('sends via the tenant_dynamic mailer when the transport is custom and writes config from the VO', function (): void {
    $factory = new FakeMailFactory(new FakeMailer);
    $config = new ConfigRepository;

    $emailSender = new LaravelEmailSender($factory, $config);

    $emailSender->sendHtml(new MailTransport(
        provider: MailProvider::Mailjet,
        host: 'in-v3.mailjet.com',
        port: MailProvider::SMTP_SUBMISSION_PORT,
        username: 'user',
        password: 'secret',
        encryption: 'tls',
        fromAddress: 'team@acme.com',
        fromName: 'Acme',
        isCustom: true,
    ), 'recipient@acme.com', 'Hello', '<p>Welcome</p>');

    expect($factory->resolvedNames)->toBe(['tenant_dynamic'])
        ->and($factory->mailer->sent)->toHaveCount(1)
        ->and($config->get('mail.mailers.tenant_dynamic'))->toBe([
            'transport' => 'smtp',
            'host' => 'in-v3.mailjet.com',
            'port' => MailProvider::SMTP_SUBMISSION_PORT,
            'username' => 'user',
            'password' => 'secret',
            'encryption' => 'tls',
        ]);
});

it('is bound to the EmailSender contract', function (): void {
    expect(app(EmailSender::class))->toBeInstanceOf(LaravelEmailSender::class);
});
