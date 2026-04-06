<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Query\GetAllFeatureFlagValuesQuery;
use App\Domain\FeatureFlag\Handler\Query\GetAllFeatureFlagValuesHandler;
use Tests\Helper\FakeFeatureFlagChecker;

it('returns all flag values from checker', function (): void {
    $checker = new FakeFeatureFlagChecker([
        'billing.stripe' => ['enabled' => true, 'value' => '1'],
        'billing.gateway' => ['enabled' => true, 'value' => 'gopay'],
    ]);

    $handler = new GetAllFeatureFlagValuesHandler($checker);

    /** @var array<string, array{enabled: bool, value: string}> $result */
    $result = $handler->handle(new GetAllFeatureFlagValuesQuery);

    expect($result)->toBe([
        'billing.stripe' => ['enabled' => true, 'value' => '1'],
        'billing.gateway' => ['enabled' => true, 'value' => 'gopay'],
    ]);
});

it('returns empty array when no flags configured', function (): void {
    $checker = new FakeFeatureFlagChecker;

    $handler = new GetAllFeatureFlagValuesHandler($checker);

    /** @var array<string, array{enabled: bool, value: string}> $result */
    $result = $handler->handle(new GetAllFeatureFlagValuesQuery);

    expect($result)->toBe([]);
});
