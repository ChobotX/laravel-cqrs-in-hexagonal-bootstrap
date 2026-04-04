<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetDefinitionBySlug;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Definition;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlug\GetDefinitionBySlugQuery;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;

/**
 * @implements QueryHandler<GetDefinitionBySlugQuery, ?Definition>
 */
final readonly class GetDefinitionBySlugHandler implements QueryHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
    ) {}

    public function handle(Query $query): ?Definition
    {
        return $this->definitionRepository->findByNamespaceAndSlug(
            new DefinitionNamespace($query->namespace),
            new DefinitionSlug($query->slug),
        );
    }
}
