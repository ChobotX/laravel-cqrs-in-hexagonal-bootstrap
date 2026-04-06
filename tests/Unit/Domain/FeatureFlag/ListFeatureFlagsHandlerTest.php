<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsQuery;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;
use App\Domain\FeatureFlag\Handler\Query\ListFeatureFlagsHandler;
use Tests\Helper\FakeFeatureFlagDefinitionProvider;
use Tests\Helper\FakeFeatureFlagOverrideRepository;

it('returns all flags with defaults when no overrides', function (): void {
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

    $handler = new ListFeatureFlagsHandler($provider, $repository);

    /** @var list<ResolvedFlag> $result */
    $result = $handler->handle(new ListFeatureFlagsQuery);

    expect($result)->toHaveCount(1)
        ->and($result[0]->definition->key->value)->toBe('billing.stripe')
        ->and($result[0]->value)->toBe('0')
        ->and($result[0]->enabled)->toBeFalse()
        ->and($result[0]->isOverridden)->toBeFalse();
});

it('returns overridden value and enabled state when override exists', function (): void {
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

    $handler = new ListFeatureFlagsHandler($provider, $repository);

    /** @var list<ResolvedFlag> $result */
    $result = $handler->handle(new ListFeatureFlagsQuery);

    expect($result)->toHaveCount(1)
        ->and($result[0]->value)->toBe('1')
        ->and($result[0]->enabled)->toBeTrue()
        ->and($result[0]->isOverridden)->toBeTrue();
});

it('returns empty list when no definitions', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider;
    $repository = new FakeFeatureFlagOverrideRepository;

    $handler = new ListFeatureFlagsHandler($provider, $repository);

    /** @var list<ResolvedFlag> $result */
    $result = $handler->handle(new ListFeatureFlagsQuery);

    expect($result)->toHaveCount(0);
});
