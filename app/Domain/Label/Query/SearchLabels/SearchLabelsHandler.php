<?php

declare(strict_types=1);

namespace App\Domain\Label\Query\SearchLabels;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelRepository;
use App\Domain\Label\LabelNamespace;

/**
 * @implements QueryHandler<SearchLabelsQuery, list<Label>>
 */
final readonly class SearchLabelsHandler implements QueryHandler
{
    public function __construct(
        private LabelRepository $labelRepository,
    ) {}

    /** @return list<Label> */
    public function handle(Query $query): array
    {
        return $this->labelRepository->search(
            new LabelNamespace($query->namespace),
            $query->term,
            $query->excludeIds,
            $query->limit,
        );
    }
}
