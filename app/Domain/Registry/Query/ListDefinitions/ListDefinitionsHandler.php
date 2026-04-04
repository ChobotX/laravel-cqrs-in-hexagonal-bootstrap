<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\ListDefinitions;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\Query\ListDefinitions\ListDefinitionsQuery;
use App\Domain\Registry\DefinitionNamespace;

/**
 * @implements QueryHandler<ListDefinitionsQuery, PaginatedResult<Definition>>
 */
final readonly class ListDefinitionsHandler implements QueryHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
    ) {}

    /** @return PaginatedResult<Definition> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = new Pagination($query->page, $query->perPage);
        $namespace = $query->namespace !== null ? new DefinitionNamespace($query->namespace) : null;

        return $this->definitionRepository->allPaginated($pagination, $namespace);
    }
}
