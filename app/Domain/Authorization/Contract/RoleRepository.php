<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;

interface RoleRepository
{
    /**
     * @param  list<Sorting>  $sortings
     * @return list<Role>
     */
    public function findAll(array $sortings = []): array;

    /**
     * @param  list<Sorting>  $sortings
     * @return PaginatedResult<Role>
     */
    public function findAllPaginated(Pagination $pagination, array $sortings = []): PaginatedResult;

    public function findById(RoleId $roleId): ?Role;

    public function findByName(string $name): ?Role;

    /** @return list<Role> */
    public function findSystemRoles(): array;

    public function create(Role $role): void;

    public function update(Role $role): void;

    public function delete(RoleId $roleId): void;

    /**
     * @param  list<string>  $excludeRoleIds
     * @return list<Role>
     */
    public function search(string $term, array $excludeRoleIds): array;

    public function count(): int;
}
