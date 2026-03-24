<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleRepository;

final class FakeRoleRepository implements RoleRepository
{
    /** @var list<Role> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, Role> $roles */
    public function __construct(
        private array $roles = [],
    ) {}

    /** @return list<Role> */
    public function findAll(): array
    {
        return array_values($this->roles);
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

    public function delete(RoleId $roleId): void
    {
        $this->deleted[] = $roleId->value;
        unset($this->roles[$roleId->value]);
    }
}
