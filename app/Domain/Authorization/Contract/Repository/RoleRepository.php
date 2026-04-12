<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Repository;

use App\Application\Filtering\Filter;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\ValueObject\RoleId;

/**
 * Persistence port for role data in the Authorization context; implementations live in Infrastructure.
 */
interface RoleRepository
{
    /**
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return list<Role>
     *                    Loads a record or value object, or null when absent.
     */
    public function findAll(array $sortings = [], array $filters = []): array;

    /**
     * @param  list<Sorting>  $sortings
     * @param  list<Filter>  $filters
     * @return PaginatedResult<Role>
     *                               Loads a record or value object, or null when absent.
     */
    public function findAllPaginated(Pagination $pagination, array $sortings = [], array $filters = []): PaginatedResult;

    /** Loads a record or value object, or null when absent. */
    public function findById(RoleId $roleId): ?Role;

    /** Loads a record or value object, or null when absent. */
    public function findByName(string $name): ?Role;

    /** @return list<Role> */
    public function findSystemRoles(): array;

    /** Persists a new or updated aggregate row. */
    public function create(Role $role): void;

    /** Contract operation `update`; see infrastructure for behavior. */
    public function update(Role $role): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(RoleId $roleId): void;

    /**
     * @param  list<string>  $excludeRoleIds
     * @return list<Role>
     *                    Returns a filtered collection according to repository rules.
     */
    public function search(string $term, array $excludeRoleIds): array;

    /** Returns the number of matching rows. */
    public function count(): int;
}
