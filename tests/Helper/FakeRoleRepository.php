<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;

final class FakeRoleRepository implements RoleRepository
{
    use PaginatesArray;
    use SortsArray;

    /** @var list<Role> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, Role> $roles */
    public function __construct(
        private array $roles = [],
    ) {}

    /** @return list<Role> */
    public function findAll(array $sortings = []): array
    {
        return $this->sortArray(array_values($this->roles), $sortings, $this->sortValueExtractor(...));
    }

    public function findById(RoleId $roleId): ?Role
    {
        return $this->roles[$roleId->value] ?? null;
    }

    public function findByName(string $name): ?Role
    {
        foreach ($this->roles as $role) {
            if ($role->name->value === $name) {
                return $role;
            }
        }

        return null;
    }

    /** @return list<Role> */
    public function findSystemRoles(): array
    {
        return array_values(array_filter(
            $this->roles,
            fn (Role $role): bool => $role->isSystem,
        ));
    }

    public function create(Role $role): void
    {
        $this->saved[] = $role;
        $this->roles[$role->id->value] = $role;
    }

    public function update(Role $role): void
    {
        $this->saved[] = $role;
        $this->roles[$role->id->value] = $role;
    }

    /** @return list<Role> */
    public function search(string $term, array $excludeRoleIds): array
    {
        $normalizedTerm = mb_strtolower($term);

        return array_values(array_filter(
            $this->roles,
            fn (Role $role): bool => ! in_array($role->id->value, $excludeRoleIds, true)
                && ($normalizedTerm === '' || str_contains(mb_strtolower($role->name->value), $normalizedTerm)),
        ));
    }

    /** @return PaginatedResult<Role> */
    public function findAllPaginated(Pagination $pagination, array $sortings = []): PaginatedResult
    {
        return $this->paginateArray($this->findAll($sortings), $pagination);
    }

    public function count(): int
    {
        return count($this->roles);
    }

    public function delete(RoleId $roleId): void
    {
        $this->deleted[] = $roleId->value;
        unset($this->roles[$roleId->value]);
    }

    /** @return callable(Role): (string|int) */
    private function sortValueExtractor(string $column): callable
    {
        return match ($column) {
            'name' => fn (Role $role): string => $role->name->value,
            Sorting::PERMISSION_SCORE => fn (Role $role): int => $role->isSystem
                ? 999999
                : array_sum(array_map(fn (RolePermission $rolePermission): int => $rolePermission->scope->order(), $role->permissions)),
            default => fn (Role $role): int => 0,
        };
    }
}
