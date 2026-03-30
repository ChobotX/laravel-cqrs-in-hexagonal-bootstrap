<?php

declare(strict_types=1);

use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;

it('uses default direction asc', function (): void {
    $sorting = new Sorting('name');

    expect($sorting->column)->toBe('name')
        ->and($sorting->direction)->toBe(SortDirection::Asc);
});

it('accepts custom column and direction', function (): void {
    $sorting = new Sorting('email', SortDirection::Desc);

    expect($sorting->column)->toBe('email')
        ->and($sorting->direction)->toBe(SortDirection::Desc);
});

it('falls back to id for empty column', function (): void {
    $sorting = new Sorting('');

    expect($sorting->column)->toBe('id');
});
