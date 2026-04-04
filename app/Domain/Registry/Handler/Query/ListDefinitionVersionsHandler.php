<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\Query\ListDefinitionVersionsQuery;

/**
 * @implements QueryHandler<ListDefinitionVersionsQuery, list<DefinitionVersion>>
 */
final readonly class ListDefinitionVersionsHandler implements QueryHandler
{
    public function __construct(
        private DefinitionVersionRepository $definitionVersionRepository,
    ) {}

    /** @return list<DefinitionVersion> */
    public function handle(Query $query): array
    {
        return $this->definitionVersionRepository->findAllByDefinition(new DefinitionId($query->definitionId));
    }
}
