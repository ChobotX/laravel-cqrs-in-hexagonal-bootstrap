<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\Query\GetTenantMailTransportQuery;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use App\Domain\Tenancy\Handler\Query\GetTenantMailTransportHandler;
use Tests\Helper\FakeTenantMailTransportRepository;

it('returns the custom transport when configured', function (): void {
    $custom = new MailTransport(
        provider: MailProvider::Mailjet,
        host: 'in-v3.mailjet.com',
        port: 587,
        username: 'user',
        password: 'secret',
        encryption: 'tls',
        fromAddress: 'team@acme.com',
        fromName: 'Acme',
        isCustom: true,
    );
    $repository = new FakeTenantMailTransportRepository(custom: $custom);
    $handler = new GetTenantMailTransportHandler($repository);

    $mailTransport = $handler->handle(new GetTenantMailTransportQuery);

    expect($mailTransport)->toBe($custom);
});

it('falls back to the default transport when no custom override exists', function (): void {
    $default = new MailTransport(
        provider: MailProvider::Custom,
        host: 'mailpit',
        port: 1025,
        username: null,
        password: null,
        encryption: null,
        fromAddress: 'no-reply@platform.dev',
        fromName: 'Platform',
        isCustom: false,
    );
    $repository = new FakeTenantMailTransportRepository(custom: null, defaultTransport: $default);
    $handler = new GetTenantMailTransportHandler($repository);

    $mailTransport = $handler->handle(new GetTenantMailTransportQuery);

    expect($mailTransport)->toBe($default)
        ->and($mailTransport->isCustom)->toBeFalse();
});
