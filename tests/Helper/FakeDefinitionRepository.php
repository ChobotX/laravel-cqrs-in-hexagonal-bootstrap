<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionRepository;
use App\Domain\Registry\Definition;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;

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

    public function findByNamespaceAndSlug(DefinitionNamespace $namespace, DefinitionSlug $slug): ?Definition
    {
        foreach ($this->definitions as $definition) {
            if ($definition->namespace->value === $namespace->value && $definition->slug->value === $slug->value) {
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
    public function allPaginated(Pagination $pagination, ?DefinitionNamespace $namespace = null): PaginatedResult
    {
        $items = array_values($this->definitions);

        if ($namespace !== null) {
            $items = array_values(array_filter(
                $items,
                fn (Definition $d): bool => $d->namespace->value === $namespace->value,
            ));
        }

        return $this->paginateArray($items, $pagination);
    }
}
