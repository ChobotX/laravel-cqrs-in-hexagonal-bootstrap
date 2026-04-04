<?php

declare(strict_types=1);

namespace App\Domain\Label\Query\GetEntityLabels;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelRepository;

/**
 * @implements QueryHandler<GetEntityLabelsQuery, list<Label>>
 */
final readonly class GetEntityLabelsHandler implements QueryHandler
{
    public function __construct(
        private LabelRepository $labelRepository,
    ) {}

    /** @return list<Label> */
    public function handle(Query $query): array
    {
        return $this->labelRepository->findByLabelableId($query->labelableId);
    }
}
