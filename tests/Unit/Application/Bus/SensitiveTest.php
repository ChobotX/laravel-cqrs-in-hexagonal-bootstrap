<?php

declare(strict_types=1);

use App\Application\Bus\Sensitive;

it('can be instantiated', function (): void {
    $attribute = new Sensitive;

    expect($attribute)->toBeInstanceOf(Sensitive::class);
});

it('targets property and parameter', function (): void {
    $reflection = new ReflectionClass(Sensitive::class);
    $attributes = $reflection->getAttributes(Attribute::class);

    expect($attributes)->toHaveCount(1);

    $attribute = $attributes[0]->newInstance();

    expect($attribute->flags)->toBe(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER);
});
