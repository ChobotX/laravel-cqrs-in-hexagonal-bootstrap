<?php

declare(strict_types=1);

use App\Application\Sorting\SortDirection;

it('has asc value', function (): void {
    expect(SortDirection::Asc->value)->toBe('asc');
});

it('has desc value', function (): void {
    expect(SortDirection::Desc->value)->toBe('desc');
});

it('can be created from string', function (): void {
    expect(SortDirection::from('asc'))->toBe(SortDirection::Asc)
        ->and(SortDirection::from('desc'))->toBe(SortDirection::Desc);
});

it('toggles asc to desc', function (): void {
    expect(SortDirection::Asc->toggle())->toBe(SortDirection::Desc);
});

it('toggles desc to asc', function (): void {
    expect(SortDirection::Desc->toggle())->toBe(SortDirection::Asc);
});
