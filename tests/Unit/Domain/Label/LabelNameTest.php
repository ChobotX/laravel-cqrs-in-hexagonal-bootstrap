<?php

declare(strict_types=1);

use App\Domain\Label\Exception\InvalidLabelNameException;
use App\Domain\Label\ValueObject\LabelName;

it('can be constructed with a valid name', function (): void {
    $name = new LabelName('important');

    expect($name->value)->toBe('important');
});

it('trims whitespace', function (): void {
    $name = new LabelName('  important  ');

    expect($name->value)->toBe('important');
});

it('rejects an empty string', function (): void {
    new LabelName('');
})->throws(InvalidLabelNameException::class, 'Value [] is not a valid label name.');

it('rejects whitespace-only string', function (): void {
    new LabelName('   ');
})->throws(InvalidLabelNameException::class, 'Value [   ] is not a valid label name.');

it('accepts a name at max length', function (): void {
    $name = new LabelName(str_repeat('a', 100));

    expect($name->value)->toBe(str_repeat('a', 100));
});

it('rejects a name exceeding max length', function (): void {
    new LabelName(str_repeat('a', 101));
})->throws(InvalidLabelNameException::class);

it('can compare equality with another LabelName', function (): void {
    $name1 = new LabelName('important');
    $name2 = new LabelName('important');
    $name3 = new LabelName('urgent');

    expect($name1->equals($name2))->toBeTrue()
        ->and($name1->equals($name3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $name = new LabelName('important');

    expect((string) $name)->toBe('important');
});
