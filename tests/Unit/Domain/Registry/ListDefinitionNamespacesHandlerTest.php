<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\Query\ListDefinitionNamespacesQuery;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Handler\Query\ListDefinitionNamespacesHandler;
use Tests\Helper\FakeDefinitionRepository;

it('returns sorted unique namespaces', function (): void {
    $repo = new FakeDefinitionRepository([
        'a' => new Definition(new DefinitionId('550e8400-e29b-41d4-a716-446655440a01'), new DefinitionNamespace('zebra'), new DefinitionSlug('one'), new DefinitionName('One')),
        'b' => new Definition(new DefinitionId('550e8400-e29b-41d4-a716-446655440a02'), new DefinitionNamespace('alpha'), new DefinitionSlug('two'), new DefinitionName('Two')),
        'c' => new Definition(new DefinitionId('550e8400-e29b-41d4-a716-446655440a03'), new DefinitionNamespace('alpha'), new DefinitionSlug('three'), new DefinitionName('Three')),
    ]);

    $handler = new ListDefinitionNamespacesHandler($repo);
    $result = $handler->handle(new ListDefinitionNamespacesQuery);

    expect($result)->toBe(['alpha', 'zebra']);
});

it('returns empty array when no definitions', function (): void {
    $handler = new ListDefinitionNamespacesHandler(new FakeDefinitionRepository);
    $result = $handler->handle(new ListDefinitionNamespacesQuery);

    expect($result)->toBe([]);
});
