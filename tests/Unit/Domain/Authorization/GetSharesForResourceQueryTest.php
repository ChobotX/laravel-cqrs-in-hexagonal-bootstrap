<?php

declare(strict_types=1);

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Authorization\Contract\Query\GetSharesForResourceQuery;

it('implements Query interface', function (): void {
    $query = new GetSharesForResourceQuery('entry', 'resource-id-1');

    expect($query)->toBeInstanceOf(Query::class);
});

it('stores resourceType and resourceId', function (): void {
    $query = new GetSharesForResourceQuery('entry', '550e8400-e29b-41d4-a716-446655440001');

    expect($query->resourceType)->toBe('entry')
        ->and($query->resourceId)->toBe('550e8400-e29b-41d4-a716-446655440001');
});

it('has SkipPermissionCheck attribute', function (): void {
    $attributes = new ReflectionClass(GetSharesForResourceQuery::class)
        ->getAttributes(SkipPermissionCheck::class);

    expect($attributes)->not->toBeEmpty();
});
