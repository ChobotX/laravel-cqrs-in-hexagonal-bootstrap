<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Query\GetDefinitionBySlug\GetDefinitionBySlugHandler;
use App\Domain\Registry\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use Tests\Helper\FakeDefinitionRepository;

it('returns a definition when found by namespace and slug', function (): void {
    $definition = new Definition(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440000'),
        new DefinitionNamespace('crm'),
        new DefinitionSlug('employees'),
        new DefinitionName('Employee Directory'),
    );

    $repo = new FakeDefinitionRepository(['550e8400-e29b-41d4-a716-446655440000' => $definition]);
    $handler = new GetDefinitionBySlugHandler($repo);

    $result = $handler->handle(new GetDefinitionBySlugQuery(namespace: 'crm', slug: 'employees'));

    assert($result instanceof Definition);
    expect($result->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('returns null when not found', function (): void {
    $repo = new FakeDefinitionRepository;
    $handler = new GetDefinitionBySlugHandler($repo);

    $result = $handler->handle(new GetDefinitionBySlugQuery(namespace: 'crm', slug: 'employees'));

    expect($result)->toBeNull();
});
