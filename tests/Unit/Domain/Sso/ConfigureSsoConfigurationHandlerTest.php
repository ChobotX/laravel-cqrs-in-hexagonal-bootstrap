<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Command\ConfigureSsoConfigurationCommand;
use App\Domain\Sso\Contract\Event\SsoConfigurationCreated;
use App\Domain\Sso\Contract\Exception\SsoConfigurationConflictException;
use App\Domain\Sso\Handler\Command\ConfigureSsoConfigurationHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\SsoFixtures;

it('creates a new configuration and emits an event', function (): void {
    $repository = new FakeSsoConfigurationRepository;
    $events = new FakeEventCollector;
    $handler = new ConfigureSsoConfigurationHandler($repository, $events);

    $handler->handle(new ConfigureSsoConfigurationCommand(
        id: SsoFixtures::CONFIG_ID,
        providerType: 'oidc',
        slug: 'primary',
        displayName: 'Primary OIDC',
        enabled: true,
        enforce: false,
        jitMode: 'invited_only',
        allowedEmailDomains: ['acme.com'],
        config: ['client_id' => 'cid'],
    ));

    expect($repository->created)->toHaveCount(1)
        ->and($repository->created[0]->slug)->toBe('primary')
        ->and($events->collected[0])->toBeInstanceOf(SsoConfigurationCreated::class);
});

it('rejects a duplicate slug for the same provider type', function (): void {
    $ssoConfiguration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$ssoConfiguration->id->value => $ssoConfiguration]);
    $handler = new ConfigureSsoConfigurationHandler($repository, new FakeEventCollector);

    $handler->handle(new ConfigureSsoConfigurationCommand(
        id: SsoFixtures::OTHER_CONFIG_ID,
        providerType: 'oidc',
        slug: 'primary',
        displayName: 'Dup',
        enabled: true,
        enforce: false,
        jitMode: 'invited_only',
        allowedEmailDomains: [],
        config: [],
    ));
})->throws(SsoConfigurationConflictException::class);
