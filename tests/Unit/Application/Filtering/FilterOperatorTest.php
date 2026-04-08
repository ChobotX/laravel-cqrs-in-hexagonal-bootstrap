<?php

declare(strict_types=1);

use App\Application\Filtering\FilterOperator;

it('has equals value', function (): void {
    expect(FilterOperator::Equals->value)->toBe('eq');
});

it('has contains value', function (): void {
    expect(FilterOperator::Contains->value)->toBe('contains');
});

it('has in value', function (): void {
    expect(FilterOperator::In->value)->toBe('in');
});

it('has search value', function (): void {
    expect(FilterOperator::Search->value)->toBe('search');
});

it('can be created from valid string values', function (): void {
    expect(FilterOperator::from('eq'))->toBe(FilterOperator::Equals)
        ->and(FilterOperator::from('contains'))->toBe(FilterOperator::Contains)
        ->and(FilterOperator::from('in'))->toBe(FilterOperator::In)
        ->and(FilterOperator::from('search'))->toBe(FilterOperator::Search);
});

it('returns null for invalid string values', function (): void {
    expect(FilterOperator::tryFrom('invalid'))->toBeNull()
        ->and(FilterOperator::tryFrom(''))->toBeNull()
        ->and(FilterOperator::tryFrom('EQUALS'))->toBeNull()
        ->and(FilterOperator::tryFrom('like'))->toBeNull();
});
