<?php

declare(strict_types=1);

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\Contract\Query\GetUsersByIdsQuery;

it('implements Query interface', function (): void {
    $query = new GetUsersByIdsQuery([]);

    expect($query)->toBeInstanceOf(Query::class);
});

it('stores userIds', function (): void {
    $ids = ['00000000-0000-0000-0000-000000000001', '00000000-0000-0000-0000-000000000002'];

    $query = new GetUsersByIdsQuery($ids);

    expect($query->userIds)->toBe($ids);
});

it('accepts empty list', function (): void {
    $query = new GetUsersByIdsQuery([]);

    expect($query->userIds)->toBe([]);
});

it('has RequiresPermission attribute', function (): void {
    $attributes = new ReflectionClass(GetUsersByIdsQuery::class)
        ->getAttributes(RequiresPermission::class);

    expect($attributes)->not->toBeEmpty();
    expect($attributes[0]->getArguments())->toBe(['users.list.read']);
});
