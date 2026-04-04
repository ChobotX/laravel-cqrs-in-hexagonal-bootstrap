<?php

declare(strict_types=1);

namespace App\Domain\Label\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\Query\GetLabelsForEntitiesQuery;
use App\Domain\Label\Contract\Repository\LabelRepository;

/** @implements QueryHandler<GetLabelsForEntitiesQuery, array<string, list<Label>>> */
final readonly class GetLabelsForEntitiesHandler implements QueryHandler
{
    public function __construct(
        private LabelRepository $labelRepository,
    ) {}

    /** @return array<string, list<Label>> */
    public function handle(Query $query): array
    {
        return $this->labelRepository->findByLabelableIds($query->entityIds);
    }
}
