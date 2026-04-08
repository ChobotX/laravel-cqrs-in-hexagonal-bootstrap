<?php

declare(strict_types=1);

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\Pagination;
use App\Infrastructure\Eloquent\User\EloquentUserRepository;
use App\Infrastructure\Eloquent\User\UserModel;

function filtersRepo(): EloquentUserRepository
{
    return app(EloquentUserRepository::class);
}

beforeEach(function (): void {
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544f001', 'name' => 'Alice Smith', 'email' => 'alice@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544f002', 'name' => 'Bob Jones', 'email' => 'bob@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544f003', 'name' => 'Charlie Brown', 'email' => 'charlie@example.com']);
});

it('filters by equals operator', function (): void {
    $filters = [new Filter('name', FilterOperator::Equals, 'Alice Smith')];
    $paginatedResult = filtersRepo()->allPaginated(new Pagination(1, 50), null, [], $filters);

    expect($paginatedResult->total)->toBe(1);
    expect($paginatedResult->items[0]->name->value)->toBe('Alice Smith');
});

it('filters by contains operator', function (): void {
    $filters = [new Filter('name', FilterOperator::Contains, 'alice')];
    $paginatedResult = filtersRepo()->allPaginated(new Pagination(1, 50), null, [], $filters);

    expect($paginatedResult->total)->toBe(1);
    expect($paginatedResult->items[0]->name->value)->toBe('Alice Smith');
});

it('filters by in operator with matching values', function (): void {
    $filters = [new Filter('name', FilterOperator::In, ['Alice Smith', 'Bob Jones'])];
    $paginatedResult = filtersRepo()->allPaginated(new Pagination(1, 50), null, [], $filters);

    expect($paginatedResult->total)->toBe(2);
});

it('returns all results for in operator with empty array', function (): void {
    $filters = [new Filter('name', FilterOperator::In, [])];
    $paginatedResult = filtersRepo()->allPaginated(new Pagination(1, 50), null, [], $filters);

    expect($paginatedResult->total)->toBe(3);
});

it('skips non-search filter with empty column', function (): void {
    $filters = [new Filter('', FilterOperator::Equals, 'value')];
    $paginatedResult = filtersRepo()->allPaginated(new Pagination(1, 50), null, [], $filters);

    expect($paginatedResult->total)->toBe(3);
});

it('returns all results for empty search term', function (): void {
    $filters = [new Filter('', FilterOperator::Search, '')];
    $paginatedResult = filtersRepo()->allPaginated(new Pagination(1, 50), null, [], $filters);

    expect($paginatedResult->total)->toBe(3);
});
