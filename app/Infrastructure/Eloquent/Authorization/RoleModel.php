<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Database\Factories\RoleModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class RoleModel extends Model
{
    /** @use HasFactory<RoleModelFactory> */
    use HasFactory;

    use HasOptimisticLocking;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'roles';

    protected $fillable = ['id', 'name', 'description', 'is_system'];

    protected $keyType = 'string';

    protected static function newFactory(): RoleModelFactory
    {
        return RoleModelFactory::new();
    }

    /** @return HasMany<RolePermissionModel, $this> */
    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermissionModel::class, 'role_id');
    }

    /** @return HasMany<UserRoleModel, $this> */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRoleModel::class, 'role_id');
    }
}
