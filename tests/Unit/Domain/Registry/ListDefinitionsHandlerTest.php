<?php

declare(strict_types=1);

use App\Application\Pagination\PaginatedResult;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Query\ListDefinitionsQuery;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Handler\Query\ListDefinitionsHandler;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;
use Tests\Helper\FakeDefinitionRepository;

it('returns paginated definitions', function (): void {
    $def1 = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employees'),
    );

    $def2 = new Definition(
        new DefinitionId('660e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('contacts'),
        new DefinitionName('Contacts'),
    );

    $repo = new FakeDefinitionRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $def1,
        '660e8400-e29b-41d4-a716-446655440000' => $def2,
    ]);
    $handler = new ListDefinitionsHandler($repo);

    $paginatedResult = $handler->handle(new ListDefinitionsQuery(page: 1, perPage: 15));

    expect($paginatedResult)->toBeInstanceOf(PaginatedResult::class)
        ->and($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->total)->toBe(2);
});

it('returns empty result when no definitions exist', function (): void {
    $repo = new FakeDefinitionRepository;
    $handler = new ListDefinitionsHandler($repo);

    $paginatedResult = $handler->handle(new ListDefinitionsQuery);

    expect($paginatedResult->items)->toHaveCount(0)
        ->and($paginatedResult->total)->toBe(0);
});

it('filters by namespace', function (): void {
    $def1 = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employees'),
    );

    $def2 = new Definition(
        new DefinitionId('660e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('hr'),
        new DefinitionSlug('positions'),
        new DefinitionName('Positions'),
    );

    $repo = new FakeDefinitionRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $def1,
        '660e8400-e29b-41d4-a716-446655440000' => $def2,
    ]);
    $handler = new ListDefinitionsHandler($repo);

    $paginatedResult = $handler->handle(new ListDefinitionsQuery(namespace: 'crm'));

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->namespace->value)->toBe('crm');
});
