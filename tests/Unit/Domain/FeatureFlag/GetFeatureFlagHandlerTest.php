<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Exception\FeatureFlagNotFoundException;
use App\Domain\FeatureFlag\Contract\Query\GetFeatureFlagQuery;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Handler\Query\GetFeatureFlagHandler;
use Tests\Helper\FakeFeatureFlagDefinitionProvider;
use Tests\Helper\FakeFeatureFlagOverrideRepository;

it('returns default value when no override exists', function (): void {
    $definition = new FlagDefinition(
        key: new FlagKey('billing.stripe'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Stripe',
        description: 'Enable Stripe',
        group: 'billing',
        groupLabel: 'Billing',
    );

    $provider = new FakeFeatureFlagDefinitionProvider([$definition]);
    $repository = new FakeFeatureFlagOverrideRepository;

    $handler = new GetFeatureFlagHandler($provider, $repository);

    $resolvedFlag = $handler->handle(new GetFeatureFlagQuery(key: 'billing.stripe'));

    expect($resolvedFlag->value)->toBe('0')
        ->and($resolvedFlag->enabled)->toBeFalse()
        ->and($resolvedFlag->isOverridden)->toBeFalse()
        ->and($resolvedFlag->definition->key->value)->toBe('billing.stripe');
});

it('returns overridden value when override exists', function (): void {
    $definition = new FlagDefinition(
        key: new FlagKey('billing.stripe'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Stripe',
        description: 'Enable Stripe',
        group: 'billing',
        groupLabel: 'Billing',
    );

    $provider = new FakeFeatureFlagDefinitionProvider([$definition]);
    $repository = new FakeFeatureFlagOverrideRepository([
        'billing.stripe' => new FeatureFlagOverride('billing.stripe', '1', true),
    ]);

    $handler = new GetFeatureFlagHandler($provider, $repository);

    $resolvedFlag = $handler->handle(new GetFeatureFlagQuery(key: 'billing.stripe'));

    expect($resolvedFlag->value)->toBe('1')
        ->and($resolvedFlag->enabled)->toBeTrue()
        ->and($resolvedFlag->isOverridden)->toBeTrue();
});

it('throws FeatureFlagNotFoundException when key does not exist', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider;
    $repository = new FakeFeatureFlagOverrideRepository;

    $handler = new GetFeatureFlagHandler($provider, $repository);

    $handler->handle(new GetFeatureFlagQuery(key: 'nonexistent.flag'));
})->throws(FeatureFlagNotFoundException::class);
