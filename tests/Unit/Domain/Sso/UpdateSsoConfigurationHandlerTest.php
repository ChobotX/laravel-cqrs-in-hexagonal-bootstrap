<?php

declare(strict_types=1);

use App\Application\Event\PropertyChangeBuilder;
use App\Domain\Sso\Contract\Command\UpdateSsoConfigurationCommand;
use App\Domain\Sso\Contract\Event\SsoConfigurationUpdated;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Handler\Command\UpdateSsoConfigurationHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\SsoFixtures;

it('updates an existing configuration', function (): void {
    $ssoConfiguration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$ssoConfiguration->id->value => $ssoConfiguration]);
    $events = new FakeEventCollector;
    $handler = new UpdateSsoConfigurationHandler($repository, $events, new PropertyChangeBuilder);

    $handler->handle(new UpdateSsoConfigurationCommand(
        id: $ssoConfiguration->id->value,
        displayName: 'Renamed',
        enabled: true,
        enforce: true,
        jitMode: 'auto_create',
        allowedEmailDomains: ['acme.com'],
        config: ['client_id' => 'new'],
    ));

    expect($repository->updated)->toHaveCount(1)
        ->and($repository->updated[0]->displayName)->toBe('Renamed')
        ->and($events->collected[0])->toBeInstanceOf(SsoConfigurationUpdated::class);
});

it('skips persistence when nothing changes', function (): void {
    $ssoConfiguration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$ssoConfiguration->id->value => $ssoConfiguration]);
    $events = new FakeEventCollector;
    $handler = new UpdateSsoConfigurationHandler($repository, $events, new PropertyChangeBuilder);

    $handler->handle(new UpdateSsoConfigurationCommand(
        id: $ssoConfiguration->id->value,
        displayName: $ssoConfiguration->displayName,
        enabled: $ssoConfiguration->enabled,
        enforce: $ssoConfiguration->enforce,
        jitMode: $ssoConfiguration->jitMode->value,
        allowedEmailDomains: $ssoConfiguration->allowedEmailDomains->domains,
        config: $ssoConfiguration->config,
    ));

    expect($repository->updated)->toBeEmpty()
        ->and($events->collected)->toBeEmpty();
});

it('throws when the configuration is missing', function (): void {
    $handler = new UpdateSsoConfigurationHandler(new FakeSsoConfigurationRepository, new FakeEventCollector, new PropertyChangeBuilder);

    $handler->handle(new UpdateSsoConfigurationCommand(
        id: SsoFixtures::CONFIG_ID,
        displayName: 'X',
        enabled: true,
        enforce: false,
        jitMode: 'invited_only',
        allowedEmailDomains: [],
        config: [],
    ));
})->throws(SsoConfigurationNotFoundException::class);
