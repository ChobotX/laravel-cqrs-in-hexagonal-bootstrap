<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\Registry;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\PaginatedResult;
use App\Contract\Http\HttpStatus;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Query\ListEntriesQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('registry.entries.read')]
final readonly class ListEntriesGridController
{
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
        $pagination = $paginationRequest->pagination();
        $search = $paginationRequest->search();
        $filters = $search !== '' ? [new Filter('', FilterOperator::Search, $search)] : [];

        return $this->queryBus->dispatch(
            new ListEntriesQuery(
                definitionId: $definitionId,
                page: $pagination->page,
                perPage: $pagination->perPage,
            )->withFilters($filters),
        );
    }
}
