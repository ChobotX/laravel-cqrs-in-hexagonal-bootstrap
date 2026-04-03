<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\ListEntries;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Entry;

/**
 * @implements QueryHandler<ListEntriesQuery, PaginatedResult<Entry>>
 */
final readonly class ListEntriesHandler implements QueryHandler
{
    public function __construct(
        private EntryRepository $entryRepository,
    ) {}

    /** @return PaginatedResult<Entry> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = new Pagination($query->page, $query->perPage);

        return $this->entryRepository->findByDefinitionPaginated(
            new DefinitionId($query->definitionId),
            $pagination,
        );
    }
}
