<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[HardDelete(reason: 'Access grant, create+revoke only, no audit trail needed')]
final class RecordShareModel extends Model
{
    use HasOptimisticLocking;
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
