<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\EntryRepository;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\Entry;

final class FakeEntryRepository implements EntryRepository
{
    use PaginatesArray;

    /** @var list<Entry> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, Entry> $entries */
    public function __construct(private array $entries = []) {}

    public function findById(EntryId $entryId): ?Entry
    {
        return $this->entries[$entryId->value] ?? null;
    }

    public function create(Entry $entry): void
    {
        $this->saved[] = $entry;
        $this->entries[$entry->id->value] = $entry;
    }

    public function update(Entry $entry): void
    {
        $this->saved[] = $entry;
        $this->entries[$entry->id->value] = $entry;
    }

    public function delete(EntryId $entryId): void
    {
        $this->deleted[] = $entryId->value;
        unset($this->entries[$entryId->value]);
    }

    /** @return PaginatedResult<Entry> */
    public function findByDefinitionPaginated(DefinitionId $definitionId, Pagination $pagination): PaginatedResult
    {
        $items = array_values(array_filter(
            $this->entries,
            fn (Entry $entry): bool => $entry->definitionId->value === $definitionId->value,
        ));

        return $this->paginateArray($items, $pagination);
    }

    public function existsByDefinition(DefinitionId $definitionId): bool
    {
        return array_any($this->entries, fn ($entry): bool => $entry->definitionId->value === $definitionId->value);
    }

    /** @return list<Entry> */
    public function findByDefinitionSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): array
    {
        return array_values(array_filter(
            $this->entries,
            fn (Entry $entry): bool => $entry->namespace->value === $definitionNamespace->value,
        ));
    }
}
