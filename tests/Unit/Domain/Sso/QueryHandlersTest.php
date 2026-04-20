<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\GetEnabledSsoProvidersQuery;
use App\Domain\Sso\Contract\Query\GetSsoConfigurationByIdQuery;
use App\Domain\Sso\Contract\Query\ListSsoConfigurationsQuery;
use App\Domain\Sso\Contract\Query\ListUserSsoIdentitiesQuery;
use App\Domain\Sso\Contract\Query\TestSsoConfigurationQuery;
use App\Domain\Sso\Contract\ValueObject\EnabledSsoProvider;
use App\Domain\Sso\Handler\Query\GetEnabledSsoProvidersHandler;
use App\Domain\Sso\Handler\Query\GetSsoConfigurationByIdHandler;
use App\Domain\Sso\Handler\Query\ListSsoConfigurationsHandler;
use App\Domain\Sso\Handler\Query\ListUserSsoIdentitiesHandler;
use App\Domain\Sso\Handler\Query\TestSsoConfigurationHandler;
use Tests\Helper\FakeSsoAuthenticator;
use Tests\Helper\FakeSsoAuthenticatorRegistry;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\FakeUserSsoIdentityRepository;
use Tests\Helper\SsoFixtures;

it('lists enabled providers in slim DTO form', function (): void {
    $ssoConfiguration = SsoFixtures::configuration(slug: 'p1');
    $disabled = SsoFixtures::configuration(id: SsoFixtures::OTHER_CONFIG_ID, slug: 'p2', enabled: false);
    $repository = new FakeSsoConfigurationRepository([
        $ssoConfiguration->id->value => $ssoConfiguration,
        $disabled->id->value => $disabled,
    ]);

    $result = new GetEnabledSsoProvidersHandler($repository)->handle(new GetEnabledSsoProvidersQuery);

    expect($result)->toHaveCount(1)
        ->and($result[0])->toBeInstanceOf(EnabledSsoProvider::class)
        ->and($result[0]->slug)->toBe('p1');
});

it('lists all configurations for the admin view', function (): void {
    $ssoConfiguration = SsoFixtures::configuration(slug: 'a');
    $b = SsoFixtures::configuration(id: SsoFixtures::OTHER_CONFIG_ID, slug: 'b');
    $repository = new FakeSsoConfigurationRepository([$ssoConfiguration->id->value => $ssoConfiguration, $b->id->value => $b]);

    $result = new ListSsoConfigurationsHandler($repository)->handle(new ListSsoConfigurationsQuery);

    expect($result)->toHaveCount(2);
});

it('returns an existing configuration by id', function (): void {
    $ssoConfiguration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$ssoConfiguration->id->value => $ssoConfiguration]);

    $result = new GetSsoConfigurationByIdHandler($repository)->handle(new GetSsoConfigurationByIdQuery($ssoConfiguration->id->value));

    expect($result)->toBeInstanceOf(SsoConfiguration::class)->and($result->id->value)->toBe($ssoConfiguration->id->value);
});

it('throws when the configuration is missing', function (): void {
    new GetSsoConfigurationByIdHandler(new FakeSsoConfigurationRepository)
        ->handle(new GetSsoConfigurationByIdQuery(SsoFixtures::CONFIG_ID));
})->throws(SsoConfigurationNotFoundException::class);

it('lists user identities', function (): void {
    $identity = SsoFixtures::identity();
    $repository = new FakeUserSsoIdentityRepository([$identity->id->value => $identity]);

    $result = new ListUserSsoIdentitiesHandler($repository)->handle(new ListUserSsoIdentitiesQuery(SsoFixtures::USER_ID));

    expect($result)->toHaveCount(1);
});

it('probes a configuration via the registered authenticator', function (): void {
    $ssoConfiguration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$ssoConfiguration->id->value => $ssoConfiguration]);
    $authenticator = new FakeSsoAuthenticator;
    $registry = new FakeSsoAuthenticatorRegistry($authenticator);

    $ssoConnectionTestResult = new TestSsoConfigurationHandler($repository, $registry)
        ->handle(new TestSsoConfigurationQuery($ssoConfiguration->id->value));

    expect($ssoConnectionTestResult->success)->toBeTrue();
});

it('rejects a missing configuration in the test query', function (): void {
    $registry = new FakeSsoAuthenticatorRegistry;

    new TestSsoConfigurationHandler(new FakeSsoConfigurationRepository, $registry)
        ->handle(new TestSsoConfigurationQuery(SsoFixtures::CONFIG_ID));
})->throws(SsoConfigurationNotFoundException::class);
