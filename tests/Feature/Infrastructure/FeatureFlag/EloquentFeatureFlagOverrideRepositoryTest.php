<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Infrastructure\Eloquent\FeatureFlag\EloquentFeatureFlagOverrideRepository;
use App\Infrastructure\Eloquent\FeatureFlag\FeatureFlagOverrideMapper;

function featureFlagRepo(): EloquentFeatureFlagOverrideRepository
{
    return new EloquentFeatureFlagOverrideRepository(new FeatureFlagOverrideMapper);
}

it('saves and finds an override by key', function (): void {
    $eloquentFeatureFlagOverrideRepository = featureFlagRepo();

    $eloquentFeatureFlagOverrideRepository->save(new FeatureFlagOverride('billing.stripe', '1', true));

    $override = $eloquentFeatureFlagOverrideRepository->findByKey(new FlagKey('billing.stripe'));

    expect($override)->not->toBeNull()
        ->and($override?->key)->toBe('billing.stripe')
        ->and($override?->value)->toBe('1');
});

it('updates an existing override', function (): void {
    $eloquentFeatureFlagOverrideRepository = featureFlagRepo();

    $eloquentFeatureFlagOverrideRepository->save(new FeatureFlagOverride('billing.stripe', '0', false));
    $eloquentFeatureFlagOverrideRepository->save(new FeatureFlagOverride('billing.stripe', '1', true));

    $override = $eloquentFeatureFlagOverrideRepository->findByKey(new FlagKey('billing.stripe'));

    expect($override?->value)->toBe('1');
});

it('returns null when key not found', function (): void {
    $eloquentFeatureFlagOverrideRepository = featureFlagRepo();

    expect($eloquentFeatureFlagOverrideRepository->findByKey(new FlagKey('nonexistent.flag')))->toBeNull();
});

it('finds all overrides', function (): void {
    $eloquentFeatureFlagOverrideRepository = featureFlagRepo();

    $eloquentFeatureFlagOverrideRepository->save(new FeatureFlagOverride('billing.stripe', '1', true));
    $eloquentFeatureFlagOverrideRepository->save(new FeatureFlagOverride('billing.gateway', 'stripe', true));

    $all = $eloquentFeatureFlagOverrideRepository->findAll();

    expect($all)->toHaveCount(2);
});

it('deletes an override by key', function (): void {
    $eloquentFeatureFlagOverrideRepository = featureFlagRepo();

    $eloquentFeatureFlagOverrideRepository->save(new FeatureFlagOverride('billing.stripe', '1', true));
    $eloquentFeatureFlagOverrideRepository->delete(new FlagKey('billing.stripe'));

    expect($eloquentFeatureFlagOverrideRepository->findByKey(new FlagKey('billing.stripe')))->toBeNull();
});
