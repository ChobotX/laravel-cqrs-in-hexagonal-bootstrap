<?php

declare(strict_types=1);

namespace App\Domain\Label\Query\GetLabelsForEntities;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelRepository;
use App\Domain\Label\Contract\Query\GetLabelsForEntities\GetLabelsForEntitiesQuery;

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
