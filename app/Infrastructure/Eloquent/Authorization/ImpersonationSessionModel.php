<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Override;

final class ImpersonationSessionModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'impersonation_sessions';

    protected $fillable = [
        'id',
        'impersonator_user_id',
        'impersonated_user_id',
        'token',
        'started_at',
        'ended_at',
    ];

    protected $keyType = 'string';

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }
}
