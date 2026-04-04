<?php

declare(strict_types=1);

use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;

it('can be constructed with value objects', function (): void {
    $label = new Label(
        id: new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        namespace: new LabelNamespace('users'),
        name: new LabelName('important'),
    );

    expect($label->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($label->namespace->value)->toBe('users')
        ->and($label->name->value)->toBe('important');
});
