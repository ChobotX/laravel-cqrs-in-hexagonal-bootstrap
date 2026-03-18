<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleRepository;
use Illuminate\Support\Facades\DB;

final readonly class EloquentRoleRepository implements RoleRepository
{
    public function __construct(
        private RoleMapper $roleMapper,
    ) {}

    /** @return list<Role> */
    public function findByOrganizationId(string $organizationId): array
    {
        $models = RoleModel::with('permissions')
            ->where('organization_id', $organizationId)
            ->get();

        return array_values(
            $models->map(fn (RoleModel $roleModel): Role => $this->roleMapper->toDomain($roleModel))->all(),
        );
    }

    public function findById(RoleId $roleId): ?Role
    {
        $model = RoleModel::with('permissions')->find($roleId->value);

        if ($model === null) {
            return null;
        }

        return $this->roleMapper->toDomain($model);
    }

    public function findByNameAndOrganization(string $name, string $organizationId): ?Role
    {
        $model = RoleModel::with('permissions')
            ->where('name', $name)
            ->where('organization_id', $organizationId)
            ->first();

        if (! $model instanceof RoleModel) {
            return null;
        }

        return $this->roleMapper->toDomain($model);
    }

    /** @return list<Role> */
    public function findSystemRoles(): array
    {
        $models = RoleModel::with('permissions')
            ->where('is_system', true)
            ->get();

        return array_values(
            $models->map(fn (RoleModel $roleModel): Role => $this->roleMapper->toDomain($roleModel))->all(),
        );
    }

    public function create(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $roleModel = new RoleModel;
            $roleModel->id = $role->id->value;
            $roleModel->organization_id = $role->organizationId;
            $roleModel->name = $role->name->value;
            $roleModel->description = $role->description;
            $roleModel->is_system = $role->isSystem;
            $roleModel->save();

            foreach ($role->permissions as $permission) {
                $roleModel->permissions()->create([
                    'module' => $permission->permissionKey->module->value,
                    'feature' => $permission->permissionKey->feature?->value,
                    'action' => $permission->permissionKey->action?->value,
                    'scope' => $permission->scope->value,
                ]);
            }
        });
    }

    public function update(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $model = RoleModel::findOrFail($role->id->value);
            $model->organization_id = $role->organizationId;
            $model->name = $role->name->value;
            $model->description = $role->description;
            $model->is_system = $role->isSystem;
            $model->save();

            $model->permissions()->delete();

            foreach ($role->permissions as $permission) {
                $model->permissions()->create([
                    'module' => $permission->permissionKey->module->value,
                    'feature' => $permission->permissionKey->feature?->value,
                    'action' => $permission->permissionKey->action?->value,
                    'scope' => $permission->scope->value,
                ]);
            }
        });
    }

    public function delete(RoleId $roleId): void
    {
        $model = RoleModel::find($roleId->value);

        if ($model instanceof RoleModel) {
            $model->delete();
        }
    }
}
