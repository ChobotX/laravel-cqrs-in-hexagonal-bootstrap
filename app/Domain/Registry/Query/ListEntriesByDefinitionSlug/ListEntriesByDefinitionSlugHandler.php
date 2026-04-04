<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\ListEntriesByDefinitionSlug;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\Entry;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\Contract\Query\ListEntriesByDefinitionSlug\ListEntriesByDefinitionSlugQuery;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;

/**
 * @implements QueryHandler<ListEntriesByDefinitionSlugQuery, list<Entry>>
 */
final readonly class ListEntriesByDefinitionSlugHandler implements QueryHandler
{
    public function __construct(
        private EntryRepository $entryRepository,
    ) {}

    /** @return list<Entry> */
    public function handle(Query $query): array
    {
        return $this->entryRepository->findByDefinitionSlug(
            new DefinitionNamespace($query->namespace),
            new DefinitionSlug($query->slug),
        );
    }
}
