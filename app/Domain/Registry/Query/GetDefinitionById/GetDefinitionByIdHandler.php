<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetDefinitionById;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Definition;

/**
 * @implements QueryHandler<GetDefinitionByIdQuery, ?Definition>
 */
final readonly class GetDefinitionByIdHandler implements QueryHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
    ) {}

    /** @return ?Definition */
    public function handle(Query $query): ?Definition
    {
        return $this->definitionRepository->findById(new DefinitionId($query->id));
    }
}
