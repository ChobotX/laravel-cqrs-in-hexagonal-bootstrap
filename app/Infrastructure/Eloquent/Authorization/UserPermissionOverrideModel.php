<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class UserPermissionOverrideModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $table = 'user_permission_overrides';

    protected $fillable = ['id', 'user_id', 'module', 'feature', 'action', 'type', 'scope'];

    protected $keyType = 'string';
}
