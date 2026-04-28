<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\Repository\TenantMailTransportRepository;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    DB::connection('tenant')->table('tenant_mail_settings')->truncate();
});

it('returns null custom transport when no row exists', function (): void {
    $tenantMailTransportRepository = app(TenantMailTransportRepository::class);

    expect($tenantMailTransportRepository->findCustom())->toBeNull();
});

it('round-trips a saved transport with encrypted password', function (): void {
    $tenantMailTransportRepository = app(TenantMailTransportRepository::class);

    $tenantMailTransportRepository->save(new MailTransport(
        provider: MailProvider::Mailjet,
        host: 'in-v3.mailjet.com',
        port: 587,
        username: 'apiuser',
        password: 'super-secret',
        encryption: 'tls',
        fromAddress: 'team@acme.com',
        fromName: 'Acme',
        isCustom: true,
    ));

    $loaded = $tenantMailTransportRepository->findCustom();

    expect($loaded)->not->toBeNull()
        ->and($loaded?->provider)->toBe(MailProvider::Mailjet)
        ->and($loaded?->host)->toBe('in-v3.mailjet.com')
        ->and($loaded?->port)->toBe(587)
        ->and($loaded?->username)->toBe('apiuser')
        ->and($loaded?->password)->toBe('super-secret')
        ->and($loaded?->encryption)->toBe('tls')
        ->and($loaded?->fromAddress)->toBe('team@acme.com')
        ->and($loaded?->fromName)->toBe('Acme')
        ->and($loaded?->isCustom)->toBeTrue();

    $stored = DB::connection('tenant')->table('tenant_mail_settings')->where('id', 1)->value('password');
    expect($stored)->not->toBe('super-secret');
});

it('clear() removes the override row', function (): void {
    $tenantMailTransportRepository = app(TenantMailTransportRepository::class);

    $tenantMailTransportRepository->save(new MailTransport(
        provider: MailProvider::Mailpit,
        host: 'mailpit',
        port: 1025,
        username: null,
        password: null,
        encryption: null,
        fromAddress: 'a@b.c',
        fromName: 'A',
        isCustom: true,
    ));

    $tenantMailTransportRepository->clear();

    expect($tenantMailTransportRepository->findCustom())->toBeNull();
});

it('returns a default transport derived from app mail config', function (): void {
    config()->set('mail.default', 'smtp');
    config()->set('mail.mailers.smtp', [
        'transport' => 'smtp',
        'host' => 'mailpit',
        'port' => 1025,
        'username' => null,
        'password' => null,
        'encryption' => null,
    ]);
    config()->set('mail.from', ['address' => 'no-reply@example.com', 'name' => 'Bootstrap']);

    $tenantMailTransportRepository = app(TenantMailTransportRepository::class);

    $default = $tenantMailTransportRepository->default();

    expect($default->host)->toBe('mailpit')
        ->and($default->port)->toBe(1025)
        ->and($default->fromAddress)->toBe('no-reply@example.com')
        ->and($default->fromName)->toBe('Bootstrap')
        ->and($default->isCustom)->toBeFalse();
});
