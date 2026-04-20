<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Query\IsSsoEnforcedQuery;
use App\Domain\Sso\Handler\Query\IsSsoEnforcedHandler;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\SsoFixtures;

it('returns false when no enforced configuration exists', function (): void {
    $result = new IsSsoEnforcedHandler(new FakeSsoConfigurationRepository)->handle(new IsSsoEnforcedQuery);

    expect($result)->toBeFalse();
});

it('returns true when at least one enabled configuration enforces', function (): void {
    $configuration = SsoFixtures::configuration(enforce: true);
    $repository = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);

    $result = new IsSsoEnforcedHandler($repository)->handle(new IsSsoEnforcedQuery);

    expect($result)->toBeTrue();
});
