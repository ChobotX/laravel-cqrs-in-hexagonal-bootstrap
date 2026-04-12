<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Repository;

use App\Application\Filtering\Filter;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

/**
 * Persistence port for entry data in the Registry context; implementations live in Infrastructure.
 */
interface EntryRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findById(EntryId $entryId): ?Entry;

    /** Persists a new or updated aggregate row. */
    public function create(Entry $entry): void;

    /** Contract operation `update`; see infrastructure for behavior. */
    public function update(Entry $entry): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(EntryId $entryId): void;

    /**
     * @param  list<Filter>  $filters
     * @param  list<Sorting>  $sortings
     * @return PaginatedResult<Entry>
     *                                Loads a record or value object, or null when absent.
     */
    public function findByDefinitionPaginated(DefinitionId $definitionId, Pagination $pagination, array $filters = [], array $sortings = []): PaginatedResult;

    /** Whether the targeted resource is present. */
    public function existsByDefinition(DefinitionId $definitionId): bool;

    /** @return list<Entry> */
    public function findByDefinitionSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): array;
}
