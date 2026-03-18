<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RolePermissionModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'role_permissions';

    protected $fillable = ['id', 'role_id', 'module', 'feature', 'action', 'scope'];

    protected $keyType = 'string';

    /** @return BelongsTo<RoleModel, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(RoleModel::class, 'role_id');
    }
}
