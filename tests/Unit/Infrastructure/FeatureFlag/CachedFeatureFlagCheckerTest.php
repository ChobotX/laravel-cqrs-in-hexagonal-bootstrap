<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Infrastructure\FeatureFlag\CachedFeatureFlagChecker;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Tests\Helper\FakeFeatureFlagDefinitionProvider;
use Tests\Helper\FakeFeatureFlagOverrideRepository;
use Tests\Helper\FakeTenantContext;

function booleanDef(): FlagDefinition
{
    return new FlagDefinition(
        key: new FlagKey('billing.stripe'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Stripe',
        description: 'Enable Stripe',
        group: 'billing',
        groupLabel: 'Billing',
    );
}

function selectDef(): FlagDefinition
{
    return new FlagDefinition(
        key: new FlagKey('billing.gateway'),
        type: FlagType::Select,
        default: 'gopay',
        defaultEnabled: true,
        label: 'Gateway',
        description: 'Payment gateway',
        group: 'billing',
        groupLabel: 'Billing',
        options: ['gopay', 'stripe'],
    );
}

/**
 * @param  list<FlagDefinition>  $definitions
 * @param  array<string, FeatureFlagOverride>  $overrides
 */
function checkerSetup(
    array $definitions = [],
    array $overrides = [],
    ?string $tenantId = 'tenant-1',
): CachedFeatureFlagChecker {
    return new CachedFeatureFlagChecker(
        featureFlagDefinitionProvider: new FakeFeatureFlagDefinitionProvider($definitions),
        featureFlagOverrideRepository: new FakeFeatureFlagOverrideRepository($overrides),
        cacheRepository: new CacheRepository(new ArrayStore),
        tenantContext: new FakeTenantContext($tenantId),
        ttl: 300,
    );
}

it('returns config defaults when no overrides', function (): void {
    $cachedFeatureFlagChecker = checkerSetup([booleanDef(), selectDef()]);

    expect($cachedFeatureFlagChecker->all())->toBe([
        'billing.stripe' => ['enabled' => false, 'value' => '0'],
        'billing.gateway' => ['enabled' => true, 'value' => 'gopay'],
    ]);
});

it('returns overridden values', function (): void {
    $cachedFeatureFlagChecker = checkerSetup(
        [booleanDef(), selectDef()],
        ['billing.stripe' => new FeatureFlagOverride('billing.stripe', '1', true)],
    );

    expect($cachedFeatureFlagChecker->all())->toBe([
        'billing.stripe' => ['enabled' => true, 'value' => '1'],
        'billing.gateway' => ['enabled' => true, 'value' => 'gopay'],
    ]);
});

it('returns defaults when tenant is not resolved', function (): void {
    $cachedFeatureFlagChecker = checkerSetup(
        [booleanDef()],
        ['billing.stripe' => new FeatureFlagOverride('billing.stripe', '1', true)],
        null,
    );

    expect($cachedFeatureFlagChecker->all())->toBe([
        'billing.stripe' => ['enabled' => false, 'value' => '0'],
    ]);
});

it('caches resolved values on second call', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $definitions = [booleanDef()];
    $overrides = ['billing.stripe' => new FeatureFlagOverride('billing.stripe', '1', true)];
    $tenantContext = new FakeTenantContext('tenant-1');

    $checker = new CachedFeatureFlagChecker(
        featureFlagDefinitionProvider: new FakeFeatureFlagDefinitionProvider($definitions),
        featureFlagOverrideRepository: new FakeFeatureFlagOverrideRepository($overrides),
        cacheRepository: $cache,
        tenantContext: $tenantContext,
        ttl: 300,
    );

    $first = $checker->all();
    $second = $checker->all();

    expect($first)->toBe($second)
        ->and($cache->has('feature-flags:tenant-1'))->toBeTrue();
});

it('isEnabled returns true for enabled flag', function (): void {
    $cachedFeatureFlagChecker = checkerSetup(
        [booleanDef()],
        ['billing.stripe' => new FeatureFlagOverride('billing.stripe', '1', true)],
    );

    expect($cachedFeatureFlagChecker->isEnabled('billing.stripe'))->toBeTrue();
});

it('isEnabled returns false for disabled flag', function (): void {
    $cachedFeatureFlagChecker = checkerSetup([booleanDef()]);

    expect($cachedFeatureFlagChecker->isEnabled('billing.stripe'))->toBeFalse();
});

it('value returns flag value', function (): void {
    $cachedFeatureFlagChecker = checkerSetup([selectDef()]);

    expect($cachedFeatureFlagChecker->value('billing.gateway'))->toBe('gopay');
});

it('value returns empty string for unknown key', function (): void {
    $cachedFeatureFlagChecker = checkerSetup([]);

    expect($cachedFeatureFlagChecker->value('unknown.flag'))->toBe('');
});

it('isEnabled returns false for unknown key', function (): void {
    $cachedFeatureFlagChecker = checkerSetup([]);

    expect($cachedFeatureFlagChecker->isEnabled('unknown.flag'))->toBeFalse();
});
