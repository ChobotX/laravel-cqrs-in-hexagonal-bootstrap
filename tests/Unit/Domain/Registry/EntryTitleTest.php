<?php

declare(strict_types=1);

use App\Domain\Registry\Exception\InvalidEntryTitleException;
use App\Domain\Registry\ValueObject\EntryTitle;

it('can be constructed with a valid title', function (): void {
    $title = new EntryTitle('John Doe');

    expect($title->value)->toBe('John Doe');
});

it('trims whitespace', function (): void {
    $title = new EntryTitle('  John Doe  ');

    expect($title->value)->toBe('John Doe');
});

it('rejects an empty string', function (): void {
    new EntryTitle('');
})->throws(InvalidEntryTitleException::class, 'Value [] is not a valid entry title.');

it('rejects whitespace-only string', function (): void {
    new EntryTitle('   ');
})->throws(InvalidEntryTitleException::class, 'Value [   ] is not a valid entry title.');

it('accepts a title at max length', function (): void {
    $title = new EntryTitle(str_repeat('a', 255));

    expect($title->value)->toBe(str_repeat('a', 255));
});

it('rejects a title exceeding max length', function (): void {
    new EntryTitle(str_repeat('a', 256));
})->throws(InvalidEntryTitleException::class);

it('can compare equality with another EntryTitle', function (): void {
    $title1 = new EntryTitle('John Doe');
    $title2 = new EntryTitle('John Doe');
    $title3 = new EntryTitle('Jane Doe');

    expect($title1->equals($title2))->toBeTrue()
        ->and($title1->equals($title3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $title = new EntryTitle('John Doe');

    expect((string) $title)->toBe('John Doe');
});
