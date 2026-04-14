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

    protected $fillable = ['id', 'name', 'email', 'password', 'password_changed_at', 'avatar_file_id'];

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
        ];
    }
}
