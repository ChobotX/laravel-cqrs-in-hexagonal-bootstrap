<?php

declare(strict_types=1);

use App\Application\Event\PropertyChangeBuilder;
use App\Domain\Tenancy\Command\UpdateTenantSettings\UpdateTenantSettingsHandler;
use App\Domain\Tenancy\Contract\Command\UpdateTenantSettingsCommand;
use App\Domain\Tenancy\Contract\Event\TenantSettingsUpdated;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use App\Domain\Tenancy\Exception\InvalidTenantDisplayTimezoneException;
use App\Domain\Tenancy\Exception\InvalidTenantNameException;
use App\Domain\Tenancy\Exception\TenantNotFoundException;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTenantSettingsRepository;

it('updates tenant name and collects TenantSettingsUpdated event', function (): void {
    $settings = new TenantSettings(name: 'Old Name', logoUrl: null, displayTimezone: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $eventCollector = new FakeEventCollector;
    $handler = new UpdateTenantSettingsHandler($repo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'New Name',
        logo: null,
        removeLogo: false,
    ));

    expect($repo->updatedName)->toBe('New Name')
        ->and($repo->updatedTenantId)->toBe('tenant-1')
        ->and($repo->updatedLogo)->toBeNull()
        ->and($repo->removedLogo)->toBeFalse()
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TenantSettingsUpdated::class);
});

it('passes logo file and remove flag to repository', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: '/old-logo.png', displayTimezone: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $eventCollector = new FakeEventCollector;
    $handler = new UpdateTenantSettingsHandler($repo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'Acme',
        logo: null,
        removeLogo: true,
    ));

    expect($repo->removedLogo)->toBeTrue()
        ->and($eventCollector->collected)->toHaveCount(1);
});

it('does nothing and collects no event when no fields change', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null, displayTimezone: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $eventCollector = new FakeEventCollector;
    $handler = new UpdateTenantSettingsHandler($repo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'Acme',
        logo: null,
        removeLogo: false,
    ));

    expect($repo->updatedName)->toBeNull()
        ->and($eventCollector->collected)->toBeEmpty();
});

it('throws when tenant not found', function (): void {
    $repo = new FakeTenantSettingsRepository;
    $handler = new UpdateTenantSettingsHandler($repo, new FakeEventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'nonexistent',
        name: 'Name',
        logo: null,
        removeLogo: false,
    ));
})->throws(TenantNotFoundException::class);

it('throws when name is empty', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null, displayTimezone: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new UpdateTenantSettingsHandler($repo, new FakeEventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: '',
        logo: null,
        removeLogo: false,
    ));
})->throws(InvalidTenantNameException::class);

it('throws when name is only whitespace', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null, displayTimezone: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new UpdateTenantSettingsHandler($repo, new FakeEventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: '   ',
        logo: null,
        removeLogo: false,
    ));
})->throws(InvalidTenantNameException::class);

it('updates display timezone and collects event', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null, displayTimezone: 'UTC');
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $eventCollector = new FakeEventCollector;
    $handler = new UpdateTenantSettingsHandler($repo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'Acme',
        logo: null,
        removeLogo: false,
        displayTimezone: 'Europe/Prague',
    ));

    expect($repo->updatedDisplayTimezone)->toBe('Europe/Prague')
        ->and($eventCollector->collected)->toHaveCount(1);
});

it('clears display timezone when empty string is sent', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null, displayTimezone: 'UTC');
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $eventCollector = new FakeEventCollector;
    $handler = new UpdateTenantSettingsHandler($repo, $eventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'Acme',
        logo: null,
        removeLogo: false,
        displayTimezone: '',
    ));

    expect($repo->updatedDisplayTimezone)->toBeNull()
        ->and($eventCollector->collected)->toHaveCount(1);
});

it('throws when display timezone is invalid', function (): void {
    $settings = new TenantSettings(name: 'Acme', logoUrl: null, displayTimezone: null);
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new UpdateTenantSettingsHandler($repo, new FakeEventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateTenantSettingsCommand(
        tenantId: 'tenant-1',
        name: 'Acme',
        logo: null,
        removeLogo: false,
        displayTimezone: 'Not/A/Zone',
    ));
})->throws(InvalidTenantDisplayTimezoneException::class);
