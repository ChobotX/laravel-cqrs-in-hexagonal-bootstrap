<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Sso;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property string $id
 * @property string $user_id
 * @property string $configuration_id
 * @property string $subject
 * @property string $email_at_link
 * @property Carbon $linked_at
 */
#[HardDelete(reason: 'Identity links are hard-deleted when an admin unlinks or deletes the configuration.')]
final class UserSsoIdentityModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'user_sso_identities';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'configuration_id',
        'subject',
        'email_at_link',
        'linked_at',
    ];

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'linked_at' => 'immutable_datetime',
        ];
    }
}
