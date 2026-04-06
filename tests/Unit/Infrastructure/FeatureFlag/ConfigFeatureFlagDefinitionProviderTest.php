<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Infrastructure\FeatureFlag\ConfigFeatureFlagDefinitionProvider;

it('parses groups into flag definitions', function (): void {
    $provider = new ConfigFeatureFlagDefinitionProvider([
        'billing' => [
            'label' => 'Billing',
            'flags' => [
                'stripe' => [
                    'type' => 'boolean',
                    'default' => false,
                    'label' => 'Stripe',
                    'description' => 'Enable Stripe',
                ],
                'gateway' => [
                    'type' => 'select',
                    'enabled' => true,
                    'default' => 'gopay',
                    'label' => 'Gateway',
                    'description' => 'Payment gateway',
                    'options' => ['gopay', 'stripe'],
                ],
            ],
        ],
    ]);

    $all = $provider->all();

    expect($all)->toHaveCount(2);

    expect($all[0]->key->value)->toBe('billing.stripe')
        ->and($all[0]->type)->toBe(FlagType::Boolean)
        ->and($all[0]->default)->toBe('0')
        ->and($all[0]->defaultEnabled)->toBeFalse()
        ->and($all[0]->label)->toBe('Stripe')
        ->and($all[0]->group)->toBe('billing')
        ->and($all[0]->groupLabel)->toBe('Billing');

    expect($all[1]->key->value)->toBe('billing.gateway')
        ->and($all[1]->type)->toBe(FlagType::Select)
        ->and($all[1]->default)->toBe('gopay')
        ->and($all[1]->defaultEnabled)->toBeTrue()
        ->and($all[1]->options)->toBe(['gopay', 'stripe']);
});

it('normalizes boolean true to string 1 and enabled true', function (): void {
    $provider = new ConfigFeatureFlagDefinitionProvider([
        'test' => [
            'label' => 'Test',
            'flags' => [
                'enabled' => [
                    'type' => 'boolean',
                    'default' => true,
                    'label' => 'Enabled',
                    'description' => 'Test flag',
                ],
            ],
        ],
    ]);

    expect($provider->all()[0]->default)->toBe('1')
        ->and($provider->all()[0]->defaultEnabled)->toBeTrue();
});

it('returns definition by key', function (): void {
    $provider = new ConfigFeatureFlagDefinitionProvider([
        'billing' => [
            'label' => 'Billing',
            'flags' => [
                'stripe' => [
                    'type' => 'boolean',
                    'default' => false,
                    'label' => 'Stripe',
                    'description' => 'Enable Stripe',
                ],
            ],
        ],
    ]);

    $definition = $provider->get('billing.stripe');

    expect($definition)->not->toBeNull();
    expect($definition?->key->value)->toBe('billing.stripe');
});

it('returns null for unknown key', function (): void {
    $provider = new ConfigFeatureFlagDefinitionProvider([]);

    expect($provider->get('nonexistent.flag'))->toBeNull();
});

it('parses input type with pattern and enabled default', function (): void {
    $provider = new ConfigFeatureFlagDefinitionProvider([
        'billing' => [
            'label' => 'Billing',
            'flags' => [
                'prefix' => [
                    'type' => 'input',
                    'enabled' => false,
                    'default' => 'INV-',
                    'label' => 'Prefix',
                    'description' => 'Invoice prefix',
                    'pattern' => '/^[A-Z]{2,5}-$/',
                ],
            ],
        ],
    ]);

    $definition = $provider->all()[0];

    expect($definition->type)->toBe(FlagType::Input)
        ->and($definition->pattern)->toBe('/^[A-Z]{2,5}-$/')
        ->and($definition->default)->toBe('INV-')
        ->and($definition->defaultEnabled)->toBeFalse();
});

it('defaults enabled to true for select without explicit enabled', function (): void {
    $provider = new ConfigFeatureFlagDefinitionProvider([
        'test' => [
            'label' => 'Test',
            'flags' => [
                'gateway' => [
                    'type' => 'select',
                    'default' => 'gopay',
                    'label' => 'Gateway',
                    'description' => 'Gateway',
                    'options' => ['gopay'],
                ],
            ],
        ],
    ]);

    expect($provider->all()[0]->defaultEnabled)->toBeTrue();
});
