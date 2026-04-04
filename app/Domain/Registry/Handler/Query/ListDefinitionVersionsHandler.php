<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Query\ListDefinitionVersionsQuery;
use App\Domain\Registry\Contract\Repository\DefinitionVersionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;

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
