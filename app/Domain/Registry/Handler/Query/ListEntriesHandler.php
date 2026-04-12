<?php

declare(strict_types=1);

namespace App\Domain\Registry\Handler\Query;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\Query\ListEntriesQuery;
use App\Domain\Registry\Contract\Repository\EntryRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;

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
            $query->filters(),
            $query->sorting(),
            $query->accessContext(),
        );
    }
}
