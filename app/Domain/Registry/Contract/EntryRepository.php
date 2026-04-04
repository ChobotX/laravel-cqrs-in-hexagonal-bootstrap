<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;

interface EntryRepository
{
    public function findById(EntryId $entryId): ?Entry;

    public function create(Entry $entry): void;

    public function update(Entry $entry): void;

    public function delete(EntryId $entryId): void;

    /** @return PaginatedResult<Entry> */
    public function findByDefinitionPaginated(DefinitionId $definitionId, Pagination $pagination): PaginatedResult;

    public function existsByDefinition(DefinitionId $definitionId): bool;

    /** @return list<Entry> */
    public function findByDefinitionSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): array;
}
