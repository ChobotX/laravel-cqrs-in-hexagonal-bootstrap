<?php

declare(strict_types=1);

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;

it('accepts valid lowercase column name', function (): void {
    $filter = new Filter('name');

    expect($filter->column)->toBe('name');
});

it('accepts column name with underscores', function (): void {
    $filter = new Filter('is_active');

    expect($filter->column)->toBe('is_active');
});

it('uses equals operator by default', function (): void {
    $filter = new Filter('status');

    expect($filter->operator)->toBe(FilterOperator::Equals);
});

it('accepts custom operator and value', function (): void {
    $filter = new Filter('name', FilterOperator::Contains, 'john');

    expect($filter->column)->toBe('name')
        ->and($filter->operator)->toBe(FilterOperator::Contains)
        ->and($filter->value)->toBe('john');
});

it('falls back to empty column for empty string', function (): void {
    $filter = new Filter('');

    expect($filter->column)->toBe('');
});

it('falls back to empty column for invalid characters', function (): void {
    expect((new Filter("'; DROP TABLE users"))->column)->toBe('')
        ->and((new Filter('column-name'))->column)->toBe('')
        ->and((new Filter('Column'))->column)->toBe('')
        ->and((new Filter('col.name'))->column)->toBe('')
        ->and((new Filter('col name'))->column)->toBe('')
        ->and((new Filter('col123'))->column)->toBe('')
        ->and((new Filter('1column'))->column)->toBe('')
        ->and((new Filter('col@name'))->column)->toBe('')
        ->and((new Filter('SELECT'))->column)->toBe('')
        ->and((new Filter("col\n"))->column)->toBe('')
        ->and((new Filter("col\t"))->column)->toBe('')
        ->and((new Filter("col\0"))->column)->toBe('');
});

it('falls back to empty column for unicode characters', function (): void {
    expect((new Filter('příjmení'))->column)->toBe('')
        ->and((new Filter('名前'))->column)->toBe('')
        ->and((new Filter('col💀'))->column)->toBe('');
});

it('forces empty column for search operator regardless of input', function (): void {
    $filter = new Filter('name', FilterOperator::Search, 'term');

    expect($filter->column)->toBe('')
        ->and($filter->operator)->toBe(FilterOperator::Search)
        ->and($filter->value)->toBe('term');
});

it('forces empty column for search operator even with valid column', function (): void {
    $filter = new Filter('email', FilterOperator::Search, 'john');

    expect($filter->column)->toBe('');
});

it('accepts list of strings for in operator', function (): void {
    $filter = new Filter('status', FilterOperator::In, ['active', 'pending']);

    expect($filter->column)->toBe('status')
        ->and($filter->operator)->toBe(FilterOperator::In)
        ->and($filter->value)->toBe(['active', 'pending']);
});

it('accepts empty array for in operator', function (): void {
    $filter = new Filter('status', FilterOperator::In, []);

    expect($filter->value)->toBe([]);
});

it('accepts single value array for in operator', function (): void {
    $filter = new Filter('status', FilterOperator::In, ['active']);

    expect($filter->value)->toBe(['active']);
});

it('accepts array with duplicates for in operator', function (): void {
    $filter = new Filter('status', FilterOperator::In, ['active', 'active']);

    expect($filter->value)->toBe(['active', 'active']);
});

it('stores special characters in value as-is', function (): void {
    expect((new Filter('name', FilterOperator::Contains, '%'))->value)->toBe('%')
        ->and((new Filter('name', FilterOperator::Contains, '_'))->value)->toBe('_')
        ->and((new Filter('name', FilterOperator::Contains, "'"))->value)->toBe("'")
        ->and((new Filter('name', FilterOperator::Contains, '"'))->value)->toBe('"')
        ->and((new Filter('name', FilterOperator::Contains, '\\'))->value)->toBe('\\')
        ->and((new Filter('name', FilterOperator::Contains, '<script>alert(1)</script>'))->value)->toBe('<script>alert(1)</script>')
        ->and((new Filter('name', FilterOperator::Contains, "\0"))->value)->toBe("\0");
});

it('stores extremely long value as-is', function (): void {
    $longValue = str_repeat('a', 1000);
    $filter = new Filter('name', FilterOperator::Contains, $longValue);

    expect($filter->value)->toBe($longValue);
});

it('stores value with leading and trailing whitespace as-is', function (): void {
    $filter = new Filter('name', FilterOperator::Equals, '  spaced  ');

    expect($filter->value)->toBe('  spaced  ');
});

it('stores unicode values as-is', function (): void {
    expect((new Filter('name', FilterOperator::Contains, 'příjmení'))->value)->toBe('příjmení')
        ->and((new Filter('name', FilterOperator::Contains, '名前'))->value)->toBe('名前')
        ->and((new Filter('name', FilterOperator::Contains, '🎉'))->value)->toBe('🎉');
});

it('stores empty string value', function (): void {
    $filter = new Filter('name', FilterOperator::Equals, '');

    expect($filter->value)->toBe('');
});

it('stores search term with sql injection attempt as-is', function (): void {
    $filter = new Filter('', FilterOperator::Search, "'; DROP TABLE users; --");

    expect($filter->value)->toBe("'; DROP TABLE users; --");
});

it('is immutable readonly', function (): void {
    $reflection = new ReflectionClass(Filter::class);

    expect($reflection->isReadOnly())->toBeTrue()
        ->and($reflection->isFinal())->toBeTrue();
});

it('accepts default empty string value for equals', function (): void {
    $filter = new Filter('status');

    expect($filter->value)->toBe('');
});
