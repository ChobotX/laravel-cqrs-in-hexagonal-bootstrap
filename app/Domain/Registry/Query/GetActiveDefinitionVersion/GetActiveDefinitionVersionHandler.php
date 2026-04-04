<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetActiveDefinitionVersion;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\DefinitionVersion;

/**
 * @implements QueryHandler<GetActiveDefinitionVersionQuery, ?DefinitionVersion>
 */
final readonly class GetActiveDefinitionVersionHandler implements QueryHandler
{
    public function __construct(
        private DefinitionVersionRepository $definitionVersionRepository,
    ) {}

    public function handle(Query $query): ?DefinitionVersion
    {
        return $this->definitionVersionRepository->findActiveByDefinition(new DefinitionId($query->definitionId));
    }
}
