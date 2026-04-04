<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Query\ListDefinitionNamespacesQuery;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;

/** @implements QueryHandler<ListDefinitionNamespacesQuery, list<string>> */
final readonly class ListDefinitionNamespacesHandler implements QueryHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
    ) {}

    /** @return list<string> */
    public function handle(Query $query): array
    {
        return $this->definitionRepository->allNamespaces();
    }
}
