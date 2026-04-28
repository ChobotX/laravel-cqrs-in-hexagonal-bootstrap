<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Command\UpdateTenantMailTransportCommand;
use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\Event\TenantMailTransportUpdated;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use App\Domain\Tenancy\Exception\InvalidMailTransportException;
use App\Domain\Tenancy\Handler\Command\UpdateTenantMailTransportHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTenantMailTransportRepository;

function validUpdateMailTransportCommand(
    ?string $host = 'in-v3.mailjet.com',
    ?int $port = 587,
    ?string $password = 'apisecret',
    ?string $fromAddress = 'team@acme.com',
    ?string $fromName = 'Acme',
    ?string $encryption = 'tls',
    ?string $username = 'apiuser',
    ?MailProvider $mailProvider = MailProvider::Mailjet,
): UpdateTenantMailTransportCommand {
    return new UpdateTenantMailTransportCommand(
        tenantId: 'tenant-1',
        useCustom: true,
        provider: $mailProvider,
        host: $host,
        port: $port,
        username: $username,
        password: $password,
        encryption: $encryption,
        fromAddress: $fromAddress,
        fromName: $fromName,
    );
}

function revertMailTransportCommand(): UpdateTenantMailTransportCommand
{
    return new UpdateTenantMailTransportCommand(
        tenantId: 'tenant-1',
        useCustom: false,
        provider: null,
        host: null,
        port: null,
        username: null,
        password: null,
        encryption: null,
        fromAddress: null,
        fromName: null,
    );
}

function existingCustomMailTransport(string $password = 'old-secret'): MailTransport
{
    return new MailTransport(
        provider: MailProvider::Mailjet,
        host: 'in-v3.mailjet.com',
        port: 587,
        username: 'apiuser',
        password: $password,
        encryption: 'tls',
        fromAddress: 'team@acme.com',
        fromName: 'Acme',
        isCustom: true,
    );
}

it('persists a brand new transport and collects an updated event', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $events = new FakeEventCollector;
    $handler = new UpdateTenantMailTransportHandler($repository, $events);

    $handler->handle(validUpdateMailTransportCommand());

    expect($repository->captured)->not->toBeNull()
        ->and($repository->captured?->host)->toBe('in-v3.mailjet.com')
        ->and($repository->captured?->password)->toBe('apisecret')
        ->and($repository->captured?->isCustom)->toBeTrue()
        ->and($events->collected)->toHaveCount(1)
        ->and($events->collected[0])->toBeInstanceOf(TenantMailTransportUpdated::class);
});

it('keeps the existing password when the incoming password is empty', function (): void {
    $repository = new FakeTenantMailTransportRepository(custom: existingCustomMailTransport());
    $events = new FakeEventCollector;
    $handler = new UpdateTenantMailTransportHandler($repository, $events);

    $handler->handle(validUpdateMailTransportCommand(password: '', fromAddress: 'new@acme.com'));

    expect($repository->captured?->password)->toBe('old-secret');
});

it('clears the override when use_custom is false and a custom transport existed', function (): void {
    $repository = new FakeTenantMailTransportRepository(custom: existingCustomMailTransport());
    $events = new FakeEventCollector;
    $handler = new UpdateTenantMailTransportHandler($repository, $events);

    $handler->handle(revertMailTransportCommand());

    expect($repository->cleared)->toBeTrue()
        ->and($repository->custom)->toBeNull()
        ->and($events->collected)->toHaveCount(1);
});

it('does nothing when use_custom is false and no override existed', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $events = new FakeEventCollector;
    $handler = new UpdateTenantMailTransportHandler($repository, $events);

    $handler->handle(revertMailTransportCommand());

    expect($repository->cleared)->toBeFalse()
        ->and($events->collected)->toHaveCount(0);
});

it('skips persistence when an unchanged custom transport is submitted with empty password', function (): void {
    $repository = new FakeTenantMailTransportRepository(custom: existingCustomMailTransport());
    $events = new FakeEventCollector;
    $handler = new UpdateTenantMailTransportHandler($repository, $events);

    $handler->handle(validUpdateMailTransportCommand(password: ''));

    expect($repository->captured)->toBeNull()
        ->and($events->collected)->toHaveCount(0);
});

it('rejects empty host', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(host: '   '));
})->throws(InvalidMailTransportException::class);

it('rejects port out of range', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(port: 70000));
})->throws(InvalidMailTransportException::class);

it('rejects missing port', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(port: null));
})->throws(InvalidMailTransportException::class);

it('rejects invalid from address', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(fromAddress: 'not-an-email'));
})->throws(InvalidMailTransportException::class);

it('rejects empty from name', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(fromName: '   '));
})->throws(InvalidMailTransportException::class);

it('rejects unknown encryption', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(encryption: 'rot13'));
})->throws(InvalidMailTransportException::class);

it('defaults the provider to Custom when null and treats blank username as null', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(encryption: null, username: '   ', mailProvider: null));

    expect($repository->captured?->provider)->toBe(MailProvider::Custom)
        ->and($repository->captured?->username)->toBeNull()
        ->and($repository->captured?->encryption)->toBeNull();
});

it('treats null username as null without trimming', function (): void {
    $repository = new FakeTenantMailTransportRepository;
    $handler = new UpdateTenantMailTransportHandler($repository, new FakeEventCollector);

    $handler->handle(validUpdateMailTransportCommand(username: null));

    expect($repository->captured?->username)->toBeNull();
});
