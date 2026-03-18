<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use Database\Factories\UserModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/** @use HasFactory<UserModelFactory> */
final class UserModel extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'users';

    protected $fillable = ['id', 'name', 'email', 'password'];

    protected $keyType = 'string';

    /** @var list<string> */
    protected $hidden = ['password'];

    protected static function newFactory(): UserModelFactory
    {
        return UserModelFactory::new();
    }
}
