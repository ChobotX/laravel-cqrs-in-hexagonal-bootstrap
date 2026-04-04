<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Exception\RoleAlreadyExistsException;
use App\Infrastructure\Eloquent\PaginatesQuery;
use App\Infrastructure\Eloquent\SortsQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

final readonly class EloquentRoleRepository implements RoleRepository
{
    use PaginatesQuery;
    use SortsQuery;

    private const int SYSTEM_ROLE_SCORE = 999999;

    private const int SCOPE_WEIGHT_ALL = 3;

    private const int SCOPE_WEIGHT_TEAM = 2;

    private const int SCOPE_WEIGHT_OWN = 1;

    public function __construct(
        private RoleMapper $roleMapper,
    ) {}

    /** @return list<Role> */
    public function findAll(array $sortings = []): array
    {
        $builder = $this->applySortings(RoleModel::with('permissions'), $sortings);

        return array_values(
            $builder->get()->map(fn (RoleModel $roleModel): Role => $this->roleMapper->toDomain($roleModel))->all(),
        );
    }

    /** @return PaginatedResult<Role> */
    public function findAllPaginated(Pagination $pagination, array $sortings = []): PaginatedResult
    {
        $builder = $this->applySortings(RoleModel::with('permissions'), $sortings);
        [$models, $total] = $this->paginateBuilder($builder, $pagination);

        return new PaginatedResult(
            array_map($this->roleMapper->toDomain(...), $models),
            $total,
            $pagination,
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

    public function findByName(string $name): ?Role
    {
        $model = RoleModel::with('permissions')
            ->where('name', $name)
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
        try {
            DB::transaction(function () use ($role): void {
                $roleModel = new RoleModel;
                $roleModel->id = $role->id->value;
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
        } catch (UniqueConstraintViolationException) {
            throw new RoleAlreadyExistsException($role->name->value);
        }
    }

    public function update(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $model = RoleModel::findOrFail($role->id->value);
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

    /** @return list<Role> */
    public function search(string $term, array $excludeRoleIds): array
    {
        $builder = RoleModel::with('permissions');

        if ($term !== '') {
            $builder->whereRaw('LOWER(name) LIKE ?', [sprintf('%%%s%%', mb_strtolower($term))]);
        }

        if ($excludeRoleIds !== []) {
            $builder->whereNotIn('id', $excludeRoleIds);
        }

        return array_values(
            $builder->get()
                ->map(fn (RoleModel $roleModel): Role => $this->roleMapper->toDomain($roleModel))
                ->all(),
        );
    }

    public function count(): int
    {
        return RoleModel::count();
    }

    public function delete(RoleId $roleId): void
    {
        $model = RoleModel::find($roleId->value);

        if ($model instanceof RoleModel) {
            $model->delete();
        }
    }

    /** @return list<string> */
    private function textSortColumns(): array
    {
        return ['name', 'description'];
    }

    /**
     * @param  Builder<RoleModel>  $builder
     * @param  list<Sorting>  $sortings
     * @return Builder<RoleModel>
     */
    private function applySortings(Builder $builder, array $sortings): Builder
    {
        $textSortings = [];

        foreach ($sortings as $sorting) {
            if ($sorting->column === Sorting::PERMISSION_SCORE) {
                $sys = self::SYSTEM_ROLE_SCORE;
                $all = self::SCOPE_WEIGHT_ALL;
                $team = self::SCOPE_WEIGHT_TEAM;
                $own = self::SCOPE_WEIGHT_OWN;

                $builder->selectRaw(<<<SQL
                    roles.*,
                    CASE WHEN roles.is_system THEN {$sys}
                    ELSE COALESCE((
                        SELECT SUM(CASE rp.scope
                            WHEN 'all' THEN {$all} WHEN 'team' THEN {$team} WHEN 'own' THEN {$own} ELSE 0
                        END)
                        FROM role_permissions rp WHERE rp.role_id = roles.id
                    ), 0) END AS permission_score
                    SQL);
                $builder->orderBy(Sorting::PERMISSION_SCORE, $sorting->direction->value);
            } else {
                $textSortings[] = $sorting;
            }
        }

        return $this->sortBuilder($builder, $textSortings);
    }
}
