<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;

it('exposes constructor fields verbatim', function (): void {
    $mailTransport = new MailTransport(
        provider: MailProvider::Mailgun,
        host: 'smtp.mailgun.org',
        port: 465,
        username: 'user@acme.com',
        password: 'secret',
        encryption: 'ssl',
        fromAddress: 'from@acme.com',
        fromName: 'Acme',
        isCustom: true,
    );

    expect($mailTransport->provider)->toBe(MailProvider::Mailgun)
        ->and($mailTransport->host)->toBe('smtp.mailgun.org')
        ->and($mailTransport->port)->toBe(465)
        ->and($mailTransport->username)->toBe('user@acme.com')
        ->and($mailTransport->password)->toBe('secret')
        ->and($mailTransport->encryption)->toBe('ssl')
        ->and($mailTransport->fromAddress)->toBe('from@acme.com')
        ->and($mailTransport->fromName)->toBe('Acme')
        ->and($mailTransport->isCustom)->toBeTrue();
});

it('lists allowed encryption values', function (): void {
    expect(MailTransport::ALLOWED_ENCRYPTIONS)->toBe(['tls', 'ssl']);
});
