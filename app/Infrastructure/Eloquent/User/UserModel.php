<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Carbon\CarbonImmutable;
use Database\Factories\UserModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Override;

/**
 * @property CarbonImmutable|null $password_changed_at
 * @property CarbonImmutable|null $email_two_factor_confirmed_at
 * @property CarbonImmutable|null $totp_confirmed_at
 * @property list<string>|null $totp_recovery_code_hashes
 */
final class UserModel extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserModelFactory> */
    use HasFactory;

    use HasOptimisticLocking;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'password_changed_at',
        'avatar_file_id',
        'email_two_factor_enabled',
        'email_two_factor_confirmed_at',
        'totp_secret',
        'totp_confirmed_at',
        'totp_recovery_code_hashes',
    ];

    protected $keyType = 'string';

    /** @var list<string> */
    protected $hidden = ['password'];

    protected static function newFactory(): UserModelFactory
    {
        return UserModelFactory::new();
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'password_changed_at' => 'immutable_datetime',
            'email_two_factor_enabled' => 'boolean',
            'email_two_factor_confirmed_at' => 'immutable_datetime',
            'totp_confirmed_at' => 'immutable_datetime',
            'totp_recovery_code_hashes' => 'array',
        ];
    }
}
