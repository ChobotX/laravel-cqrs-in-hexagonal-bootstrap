<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\ListDefinitionVersions;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\DefinitionVersion;

/**
 * @implements QueryHandler<ListDefinitionVersionsQuery, list<DefinitionVersion>>
 */
final readonly class ListDefinitionVersionsHandler implements QueryHandler
{
    public function __construct(
        private DefinitionVersionRepository $versionRepository,
    ) {}

    /** @return list<DefinitionVersion> */
    public function handle(Query $query): array
    {
        return $this->versionRepository->findAllByDefinition(new DefinitionId($query->definitionId));
    }
}
