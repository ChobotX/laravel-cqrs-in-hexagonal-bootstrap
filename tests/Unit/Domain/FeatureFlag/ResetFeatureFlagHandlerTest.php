<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Command\ResetFeatureFlagCommand;
use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagReset;
use App\Domain\FeatureFlag\Contract\Exception\FeatureFlagNotFoundException;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Handler\Command\ResetFeatureFlagHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeFeatureFlagCacheInvalidator;
use Tests\Helper\FakeFeatureFlagDefinitionProvider;
use Tests\Helper\FakeFeatureFlagOverrideRepository;

it('deletes override and collects event', function (): void {
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
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new ResetFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new ResetFeatureFlagCommand(key: 'billing.stripe'));

    expect($repository->deleted)->toHaveCount(1)
        ->and($repository->deleted[0])->toBe('billing.stripe')
        ->and($cacheInvalidator->invalidateCount)->toBe(1)
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(FeatureFlagReset::class);

    assert($eventCollector->collected[0] instanceof FeatureFlagReset);
    expect($eventCollector->collected[0]->key)->toBe('billing.stripe')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws FeatureFlagNotFoundException when key does not exist', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider;
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new ResetFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new ResetFeatureFlagCommand(key: 'nonexistent.flag'));
})->throws(FeatureFlagNotFoundException::class);
