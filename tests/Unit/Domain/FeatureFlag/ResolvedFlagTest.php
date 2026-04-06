<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;

it('isEnabled returns true when enabled', function (): void {
    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
    );

    $flag = new ResolvedFlag($definition, '1', true, true);

    expect($flag->isEnabled())->toBeTrue();
});

it('isEnabled returns false when disabled', function (): void {
    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Boolean,
        default: '0',
        defaultEnabled: false,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
    );

    $flag = new ResolvedFlag($definition, '0', false, false);

    expect($flag->isEnabled())->toBeFalse();
});

it('isEnabled returns true for enabled select flag', function (): void {
    $definition = new FlagDefinition(
        key: new FlagKey('test.flag'),
        type: FlagType::Select,
        default: 'gopay',
        defaultEnabled: true,
        label: 'Test',
        description: 'Test',
        group: 'test',
        groupLabel: 'Test',
        options: ['gopay', 'stripe'],
    );

    $flag = new ResolvedFlag($definition, 'stripe', true, true);

    expect($flag->isEnabled())->toBeTrue();
});
