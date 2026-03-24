<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class RecordShareModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $table = 'record_shares';

    protected $fillable = [
        'id',
        'grantee_user_id',
        'resource_type',
        'resource_id',
        'action',
        'grantor_user_id',
    ];

    protected $keyType = 'string';
}
