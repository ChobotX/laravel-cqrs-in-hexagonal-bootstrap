<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Command\DeleteSsoConfigurationCommand;
use App\Domain\Sso\Contract\Event\SsoConfigurationDeleted;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Handler\Command\DeleteSsoConfigurationHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\FakeUserSsoIdentityRepository;
use Tests\Helper\SsoFixtures;

it('deletes a configuration and its identities', function (): void {
    $ssoConfiguration = SsoFixtures::configuration();
    $configurationRepository = new FakeSsoConfigurationRepository([$ssoConfiguration->id->value => $ssoConfiguration]);
    $identityRepository = new FakeUserSsoIdentityRepository;
    $events = new FakeEventCollector;
    $handler = new DeleteSsoConfigurationHandler($configurationRepository, $identityRepository, $events);

    $handler->handle(new DeleteSsoConfigurationCommand(id: $ssoConfiguration->id->value));

    expect($configurationRepository->deleted)->toBe([$ssoConfiguration->id->value])
        ->and($identityRepository->bulkDeletedConfigurationIds)->toBe([$ssoConfiguration->id->value])
        ->and($events->collected[0])->toBeInstanceOf(SsoConfigurationDeleted::class);
});

it('throws when the configuration is missing', function (): void {
    $handler = new DeleteSsoConfigurationHandler(new FakeSsoConfigurationRepository, new FakeUserSsoIdentityRepository, new FakeEventCollector);

    $handler->handle(new DeleteSsoConfigurationCommand(id: SsoFixtures::CONFIG_ID));
})->throws(SsoConfigurationNotFoundException::class);
