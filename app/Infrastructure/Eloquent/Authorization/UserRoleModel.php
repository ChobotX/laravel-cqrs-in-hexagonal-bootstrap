<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserRoleModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'user_roles';

    protected $fillable = ['id', 'user_id', 'role_id', 'organization_id'];

    protected $keyType = 'string';

    /** @return BelongsTo<RoleModel, $this> */
    public function role(): BelongsTo
    {
        return $this->belongsTo(RoleModel::class, 'role_id');
    }
}
