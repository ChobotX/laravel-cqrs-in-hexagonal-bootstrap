<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Service\DefaultFeatureFlagChecker;
use Tests\Helper\FakeFeatureFlagDefinitionProvider;
use Tests\Helper\FakeFeatureFlagOverrideRepository;

it('merges overrides over defaults', function (): void {
    $checker = new DefaultFeatureFlagChecker(
        new FakeFeatureFlagDefinitionProvider([
            new FlagDefinition(
                key: new FlagKey('a.b'),
                type: FlagType::Boolean,
                default: '0',
                defaultEnabled: false,
                label: 'L',
                description: 'D',
                group: 'g',
                groupLabel: 'G',
            ),
        ]),
        new FakeFeatureFlagOverrideRepository([
            'a.b' => new FeatureFlagOverride('a.b', '1', true),
        ]),
    );

    expect($checker->all())->toBe(['a.b' => ['enabled' => true, 'value' => '1']]);
});

it('returns definition defaults when no overrides are present', function (): void {
    $checker = new DefaultFeatureFlagChecker(
        new FakeFeatureFlagDefinitionProvider([
            new FlagDefinition(
                key: new FlagKey('a.b'),
                type: FlagType::Boolean,
                default: '0',
                defaultEnabled: false,
                label: 'L',
                description: 'D',
                group: 'g',
                groupLabel: 'G',
            ),
        ]),
        new FakeFeatureFlagOverrideRepository([]),
    );

    expect($checker->all())->toBe(['a.b' => ['enabled' => false, 'value' => '0']]);
});

it('exposes enabled state and value for a known key', function (): void {
    $checker = new DefaultFeatureFlagChecker(
        new FakeFeatureFlagDefinitionProvider([
            new FlagDefinition(
                key: new FlagKey('a.b'),
                type: FlagType::Boolean,
                default: 'x',
                defaultEnabled: true,
                label: 'L',
                description: 'D',
                group: 'g',
                groupLabel: 'G',
            ),
        ]),
        new FakeFeatureFlagOverrideRepository([]),
    );

    expect($checker->isEnabled('a.b'))->toBeTrue()
        ->and($checker->value('a.b'))->toBe('x');
});

it('treats unknown keys as disabled with empty value', function (): void {
    $checker = new DefaultFeatureFlagChecker(
        new FakeFeatureFlagDefinitionProvider([
            new FlagDefinition(
                key: new FlagKey('a.b'),
                type: FlagType::Boolean,
                default: '0',
                defaultEnabled: false,
                label: 'L',
                description: 'D',
                group: 'g',
                groupLabel: 'G',
            ),
        ]),
        new FakeFeatureFlagOverrideRepository([]),
    );

    expect($checker->isEnabled('missing'))->toBeFalse()
        ->and($checker->value('missing'))->toBe('');
});
