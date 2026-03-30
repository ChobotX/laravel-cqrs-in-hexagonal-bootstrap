<?php

declare(strict_types=1);

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;

it('exposes items and metadata', function (): void {
    $result = new PaginatedResult(['a', 'b', 'c'], 10, new Pagination(2, 3));

    expect($result->items)->toBe(['a', 'b', 'c'])
        ->and($result->total)->toBe(10)
        ->and($result->pagination->page)->toBe(2)
        ->and($result->pagination->perPage)->toBe(3);
});

it('calculates total pages', function (): void {
    expect(new PaginatedResult([], 0, new Pagination(1, 15))->totalPages())->toBe(1)
        ->and(new PaginatedResult([], 1, new Pagination(1, 15))->totalPages())->toBe(1)
        ->and(new PaginatedResult([], 15, new Pagination(1, 15))->totalPages())->toBe(1)
        ->and(new PaginatedResult([], 16, new Pagination(1, 15))->totalPages())->toBe(2)
        ->and(new PaginatedResult([], 45, new Pagination(1, 15))->totalPages())->toBe(3)
        ->and(new PaginatedResult([], 46, new Pagination(1, 15))->totalPages())->toBe(4);
});

it('detects next page', function (): void {
    expect(new PaginatedResult([], 30, new Pagination(1, 15))->hasNextPage())->toBeTrue()
        ->and(new PaginatedResult([], 30, new Pagination(2, 15))->hasNextPage())->toBeFalse()
        ->and(new PaginatedResult([], 0, new Pagination(1, 15))->hasNextPage())->toBeFalse();
});

it('detects previous page', function (): void {
    expect(new PaginatedResult([], 30, new Pagination(1, 15))->hasPreviousPage())->toBeFalse()
        ->and(new PaginatedResult([], 30, new Pagination(2, 15))->hasPreviousPage())->toBeTrue()
        ->and(new PaginatedResult([], 30, new Pagination(3, 15))->hasPreviousPage())->toBeTrue();
});

it('handles single page of results', function (): void {
    $result = new PaginatedResult(['a', 'b'], 2, new Pagination(1, 15));

    expect($result->totalPages())->toBe(1)
        ->and($result->hasNextPage())->toBeFalse()
        ->and($result->hasPreviousPage())->toBeFalse();
});

it('handles exact boundary', function (): void {
    $result = new PaginatedResult([], 30, new Pagination(2, 15));

    expect($result->totalPages())->toBe(2)
        ->and($result->hasNextPage())->toBeFalse()
        ->and($result->hasPreviousPage())->toBeTrue();
});
