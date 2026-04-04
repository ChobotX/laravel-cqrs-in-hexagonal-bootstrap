<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;

interface DefinitionRepository
{
    public function findById(DefinitionId $definitionId): ?Definition;

    public function findByNamespaceAndSlug(DefinitionNamespace $definitionNamespace, DefinitionSlug $definitionSlug): ?Definition;

    public function create(Definition $definition): void;

    public function update(Definition $definition): void;

    public function delete(DefinitionId $definitionId): void;

    /** @return PaginatedResult<Definition> */
    public function allPaginated(Pagination $pagination, ?DefinitionNamespace $definitionNamespace = null): PaginatedResult;

    /** @return list<string> */
    public function allNamespaces(): array;
}
