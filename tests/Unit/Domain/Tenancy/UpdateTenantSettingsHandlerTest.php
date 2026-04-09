<?php

declare(strict_types=1);

use App\Domain\Tenancy\Command\UpdateTenantSettings\UpdateTenantSettingsHandler;
use App\Domain\Tenancy\Contract\Command\UpdateTenantSettingsCommand;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use App\Domain\Tenancy\Exception\InvalidTenantNameException;
use App\Domain\Tenancy\Exception\TenantNotFoundException;
use Tests\Helper\FakeTenantSettingsRepository;

it('updates tenant name', function (): void {
    $settings = new TenantSettings(name: 'Old Name', logoUrl: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new UpdateTenantSettingsHandler($repo);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'New Name',
        logo: null,
        removeLogo: false,
    ));

    expect($repo->updatedName)->toBe('New Name')
        ->and($repo->updatedTenantId)->toBe('tenant-1')
        ->and($repo->updatedLogo)->toBeNull()
        ->and($repo->removedLogo)->toBeFalse();
});

it('passes logo file and remove flag to repository', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: '/old-logo.png');
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new UpdateTenantSettingsHandler($repo);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'Acme',
        logo: null,
        removeLogo: true,
    ));

    expect($repo->removedLogo)->toBeTrue();
});

it('throws when tenant not found', function (): void {
    $repo = new FakeTenantSettingsRepository;
    $handler = new UpdateTenantSettingsHandler($repo);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'nonexistent',
        name: 'Name',
        logo: null,
        removeLogo: false,
    ));
})->throws(TenantNotFoundException::class);

it('throws when name is empty', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new UpdateTenantSettingsHandler($repo);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: '',
        logo: null,
        removeLogo: false,
    ));
})->throws(InvalidTenantNameException::class);

it('throws when name is only whitespace', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new UpdateTenantSettingsHandler($repo);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: '   ',
        logo: null,
        removeLogo: false,
    ));
})->throws(InvalidTenantNameException::class);
