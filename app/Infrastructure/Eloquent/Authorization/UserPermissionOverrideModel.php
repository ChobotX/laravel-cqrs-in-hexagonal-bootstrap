<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[HardDelete(reason: 'Permission override, create+revoke only, invalidated by version counter')]
final class UserPermissionOverrideModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'user_permission_overrides';

    protected $fillable = ['id', 'user_id', 'module', 'feature', 'action', 'type', 'scope'];

    protected $keyType = 'string';
}
