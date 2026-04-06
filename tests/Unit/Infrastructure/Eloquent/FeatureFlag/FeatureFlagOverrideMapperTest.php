<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\FeatureFlag\FeatureFlagOverrideMapper;
use App\Infrastructure\Eloquent\FeatureFlag\FeatureFlagOverrideModel;
use Tests\TestCase;

uses(TestCase::class);

it('maps a feature flag override model to domain', function (): void {
    $model = new FeatureFlagOverrideModel;
    $model->key = 'billing.stripe';
    $model->enabled = true;
    $model->value = '1';

    $mapper = new FeatureFlagOverrideMapper;
    $featureFlagOverride = $mapper->toDomain($model);

    expect($featureFlagOverride->key)->toBe('billing.stripe')
        ->and($featureFlagOverride->value)->toBe('1')
        ->and($featureFlagOverride->enabled)->toBeTrue();
});
