<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Label;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\Query\SearchLabelsQuery;
use App\Presentation\Http\Request\Web\Label\SearchLabelsRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('labels.management.read')]
final readonly class SearchLabelsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(SearchLabelsRequest $searchLabelsRequest): JsonResponse
    {
        /** @var list<Label> $labels */
        $labels = $this->queryBus->dispatch(new SearchLabelsQuery(
            namespace: $searchLabelsRequest->namespace(),
            term: $searchLabelsRequest->searchTerm(),
            excludeIds: $searchLabelsRequest->excludeLabelIds(),
        ));

        $data = array_map(fn (Label $label): array => [
            'id' => $label->id->value,
            'name' => $label->name->value,
        ], $labels);

        return new JsonResponse(['data' => $data]);
    }
}
