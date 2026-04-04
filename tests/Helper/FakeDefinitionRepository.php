<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\Repository\DefinitionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

final class FakeDefinitionRepository implements DefinitionRepository
{
    use PaginatesArray;

    /** @var list<Definition> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, Definition> $definitions */
    public function __construct(private array $definitions = []) {}

    public function findById(DefinitionId $definitionId): ?Definition
    {
        return $this->definitions[$definitionId->value] ?? null;
    }

    public function findByNamespaceAndSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): ?Definition
    {
        foreach ($this->definitions as $definition) {
            if ($definition->namespace->value === $definitionNamespace->value && $definition->slug->value === $definitionSlug->value) {
                return $definition;
            }
        }

        return null;
    }

    public function create(Definition $definition): void
    {
        $this->saved[] = $definition;
        $this->definitions[$definition->id->value] = $definition;
    }

    public function update(Definition $definition): void
    {
        $this->saved[] = $definition;
        $this->definitions[$definition->id->value] = $definition;
    }

    public function delete(DefinitionId $definitionId): void
    {
        $this->deleted[] = $definitionId->value;
        unset($this->definitions[$definitionId->value]);
    }

    /** @return PaginatedResult<Definition> */
    public function allPaginated(Pagination $pagination, ?DefinitionNamespace $definitionNamespace = null): PaginatedResult
    {
        $items = array_values($this->definitions);

        if ($definitionNamespace instanceof DefinitionNamespace) {
            $items = array_values(array_filter(
                $items,
                fn (Definition $definition): bool => $definition->namespace->value === $definitionNamespace->value,
            ));
        }

        return $this->paginateArray($items, $pagination);
    }

    /** @return list<string> */
    public function allNamespaces(): array
    {
        $namespaces = array_unique(array_map(
            fn (Definition $definition): string => $definition->namespace->value,
            $this->definitions,
        ));
        sort($namespaces);

        return $namespaces;
    }
}
