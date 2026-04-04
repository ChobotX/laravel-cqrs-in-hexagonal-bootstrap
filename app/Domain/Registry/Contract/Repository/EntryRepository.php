<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Repository;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\Entity\Entry;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\EntryId;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

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
