<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Query\GetDefinitionByIdQuery;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;

/**
 * @implements QueryHandler<GetDefinitionByIdQuery, ?Definition>
 */
final readonly class GetDefinitionByIdHandler implements QueryHandler
{
    public function __construct(
        private DefinitionRepository $definitionRepository,
    ) {}

    public function handle(Query $query): ?Definition
    {
        return $this->definitionRepository->findById(new DefinitionId($query->id));
    }
}
