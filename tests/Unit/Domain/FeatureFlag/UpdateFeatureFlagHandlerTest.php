<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Domain\FeatureFlag\Constant\FeatureFlagFields;
use App\Domain\FeatureFlag\Contract\Command\UpdateFeatureFlagCommand;
use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagUpdated;
use App\Domain\FeatureFlag\Contract\Exception\FeatureFlagNotFoundException;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Exception\InvalidFlagValueException;
use App\Domain\FeatureFlag\Handler\Command\UpdateFeatureFlagHandler;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeFeatureFlagCacheInvalidator;
use Tests\Helper\FakeFeatureFlagDefinitionProvider;
use Tests\Helper\FakeFeatureFlagOverrideRepository;

function booleanDefinition(): FlagDefinition
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

function selectDefinition(): FlagDefinition
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

function inputDefinition(): FlagDefinition
{
    return new FlagDefinition(
        key: new FlagKey('billing.prefix'),
        type: FlagType::Input,
        default: 'INV-',
        defaultEnabled: false,
        label: 'Prefix',
        description: 'Invoice prefix',
        group: 'billing',
        groupLabel: 'Billing',
        pattern: '/^[A-Z]{2,5}-$/',
    );
}

it('saves boolean override and collects event', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([booleanDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.stripe', enabled: true));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->key)->toBe('billing.stripe')
        ->and($repository->saved[0]->value)->toBe('1')
        ->and($repository->saved[0]->enabled)->toBeTrue()
        ->and($cacheInvalidator->invalidateCount)->toBe(1)
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(FeatureFlagUpdated::class);

    assert($eventCollector->collected[0] instanceof FeatureFlagUpdated);
    expect($eventCollector->collected[0]->key)->toBe('billing.stripe')
        ->and($eventCollector->collected[0]->changes())->toEqual([
            new PropertyChange(FeatureFlagFields::VALUE, null, '1'),
            new PropertyChange(FeatureFlagFields::ENABLED, null, true),
        ])
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('does not save or collect event when data is unchanged', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([booleanDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository([
        'billing.stripe' => new FeatureFlagOverride('billing.stripe', '1', true),
    ]);
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.stripe', enabled: true));

    expect($repository->saved)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0)
        ->and($cacheInvalidator->invalidateCount)->toBe(0);
});

it('disables boolean flag with auto-derived value', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([booleanDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.stripe', enabled: false));

    expect($repository->saved[0]->value)->toBe('0')
        ->and($repository->saved[0]->enabled)->toBeFalse();
});

it('saves select override with explicit value', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([selectDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.gateway', enabled: true, value: 'stripe'));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->value)->toBe('stripe')
        ->and($repository->saved[0]->enabled)->toBeTrue();
});

it('toggles select flag off keeping existing value', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([selectDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository([
        'billing.gateway' => new FeatureFlagOverride('billing.gateway', 'stripe', true),
    ]);
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.gateway', enabled: false));

    expect($repository->saved[0]->value)->toBe('stripe')
        ->and($repository->saved[0]->enabled)->toBeFalse();
});

it('toggles select flag off using default when no override exists', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([selectDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.gateway', enabled: false));

    expect($repository->saved[0]->value)->toBe('gopay')
        ->and($repository->saved[0]->enabled)->toBeFalse();
});

it('saves input override matching pattern', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([inputDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.prefix', enabled: true, value: 'FAK-'));

    expect($repository->saved)->toHaveCount(1)
        ->and($repository->saved[0]->value)->toBe('FAK-')
        ->and($repository->saved[0]->enabled)->toBeTrue();
});

it('throws FeatureFlagNotFoundException when key does not exist', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider;
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'nonexistent.flag', enabled: true));
})->throws(FeatureFlagNotFoundException::class);

it('throws InvalidFlagValueException for invalid boolean value', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([booleanDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.stripe', enabled: true, value: 'yes'));
})->throws(InvalidFlagValueException::class);

it('throws InvalidFlagValueException for invalid select value', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([selectDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.gateway', enabled: true, value: 'paypal'));
})->throws(InvalidFlagValueException::class);

it('throws InvalidFlagValueException for invalid input pattern', function (): void {
    $provider = new FakeFeatureFlagDefinitionProvider([inputDefinition()]);
    $repository = new FakeFeatureFlagOverrideRepository;
    $cacheInvalidator = new FakeFeatureFlagCacheInvalidator;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateFeatureFlagHandler($provider, $repository, $cacheInvalidator, $eventCollector);

    $handler->handle(new UpdateFeatureFlagCommand(key: 'billing.prefix', enabled: true, value: 'invalid'));
})->throws(InvalidFlagValueException::class);
