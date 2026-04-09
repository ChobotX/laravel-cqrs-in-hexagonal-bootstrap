<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\PaginatedResult;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Query\ListEntriesQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesGridController
{
    private const array SORTABLE_COLUMNS = ['title', 'version'];

    private const array COLUMN_MAP = ['version' => 'definition_version'];

    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest, string $namespace, string $slug): JsonResponse
    {
        $definition = $this->queryBus->dispatch(new GetDefinitionBySlugQuery($namespace, $slug));

        abort_unless($definition !== null, HttpStatus::NOT_FOUND);

        $paginatedResult = $this->fetchEntries($paginationRequest, $definition->id->value);

        return new JsonResponse([
            'data' => array_map(fn (Entry $entry): array => [
                'id' => $entry->id->value,
                'title' => $entry->title->value,
                'version' => $entry->definitionVersion->value,
                'edit_url' => route('registry.entries.edit', [$namespace, $slug, $entry->id->value]),
                'delete_url' => route('registry.entries.destroy', [$namespace, $slug, $entry->id->value]),
            ], $paginatedResult->items),
            'meta' => [
                'current_page' => $paginatedResult->pagination->page,
                'per_page' => $paginatedResult->pagination->perPage,
                'total' => $paginatedResult->total,
                'total_pages' => $paginatedResult->totalPages(),
            ],
        ]);
    }

    /** @return PaginatedResult<Entry> */
    private function fetchEntries(PaginationRequest $paginationRequest, string $definitionId): PaginatedResult
    {
        $sorting = $this->resolveSorting($paginationRequest);
        $search = $paginationRequest->search();
        $filters = $search !== '' ? [new Filter('', FilterOperator::Search, $search)] : [];

        return $this->queryBus->dispatch(
            new ListEntriesQuery(
                definitionId: $definitionId,
                page: $paginationRequest->pagination()->page,
                perPage: $paginationRequest->pagination()->perPage,
            )->withFilters($filters)->withSorting([$sorting]),
        );
    }

    private function resolveSorting(PaginationRequest $paginationRequest): Sorting
    {
        $defaultSorting = new Sorting('title', SortDirection::Asc);
        $requestSorting = $paginationRequest->sorting();

        if (! $requestSorting instanceof Sorting || ! in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)) {
            return $defaultSorting;
        }

        $dbColumn = self::COLUMN_MAP[$requestSorting->column] ?? $requestSorting->column;

        return new Sorting($dbColumn, $requestSorting->direction);
    }
}
