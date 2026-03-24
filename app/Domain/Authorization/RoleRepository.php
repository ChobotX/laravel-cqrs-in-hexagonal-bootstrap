<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

interface RoleRepository
{
    /** @return list<Role> */
    public function findAll(): array;

    public function findById(RoleId $roleId): ?Role;

    public function findByName(string $name): ?Role;

    /** @return list<Role> */
    public function findSystemRoles(): array;

    public function create(Role $role): void;

    public function update(Role $role): void;

    public function delete(RoleId $roleId): void;
}
