<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Repository;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\Entity\Definition;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

/**
 * Persistence port for definition data in the Registry context; implementations live in Infrastructure.
 */
interface DefinitionRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findById(DefinitionId $definitionId): ?Definition;

    /** Loads a record or value object, or null when absent. */
    public function findByNamespaceAndSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): ?Definition;

    /** Persists a new or updated aggregate row. */
    public function create(Definition $definition): void;

    /** Contract operation `update`; see infrastructure for behavior. */
    public function update(Definition $definition): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(DefinitionId $definitionId): void;

    /** @return PaginatedResult<Definition> */
    public function allPaginated(Pagination $pagination, ?DefinitionNamespace $definitionNamespace = null): PaginatedResult;

    /** @return list<string> */
    public function allNamespaces(): array;
}
