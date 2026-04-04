<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

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
