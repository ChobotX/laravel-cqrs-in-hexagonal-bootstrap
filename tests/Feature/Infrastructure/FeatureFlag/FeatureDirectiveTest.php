<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;
use Illuminate\Support\Facades\Blade;
use Tests\Helper\FakeFeatureFlagChecker;

it('feature directive renders content when flag is enabled', function (): void {
    $this->app->instance(FeatureFlagChecker::class, new FakeFeatureFlagChecker([
        'billing.stripe' => ['enabled' => true, 'value' => '1'],
    ]));

    $rendered = Blade::render('@feature("billing.stripe") YES @else NO @endfeature');

    expect(trim($rendered))->toBe('YES');
});

it('feature directive renders else content when flag is disabled', function (): void {
    $this->app->instance(FeatureFlagChecker::class, new FakeFeatureFlagChecker([
        'billing.stripe' => ['enabled' => false, 'value' => '0'],
    ]));

    $rendered = Blade::render('@feature("billing.stripe") YES @else NO @endfeature');

    expect(trim($rendered))->toBe('NO');
});

it('featureValue directive renders content when value matches', function (): void {
    $this->app->instance(FeatureFlagChecker::class, new FakeFeatureFlagChecker([
        'billing.gateway' => ['enabled' => true, 'value' => 'stripe'],
    ]));

    $rendered = Blade::render('@featureValue("billing.gateway", "stripe") STRIPE @else OTHER @endfeatureValue');

    expect(trim($rendered))->toBe('STRIPE');
});

it('featureValue directive renders else when value does not match', function (): void {
    $this->app->instance(FeatureFlagChecker::class, new FakeFeatureFlagChecker([
        'billing.gateway' => ['enabled' => true, 'value' => 'gopay'],
    ]));

    $rendered = Blade::render('@featureValue("billing.gateway", "stripe") STRIPE @else OTHER @endfeatureValue');

    expect(trim($rendered))->toBe('OTHER');
});
