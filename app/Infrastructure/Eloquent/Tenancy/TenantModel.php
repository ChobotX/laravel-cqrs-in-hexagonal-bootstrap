<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Tenancy;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

final class TenantModel extends Model
{
    use HasUuids;
    use SoftDeletes;

    /** @var string */
    protected $connection = 'landlord';

    protected $table = 'tenants';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'schema_name',
        'database_host',
        'database_port',
        'database_name',
        'database_username',
        'database_password',
        'is_active',
        'config',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'config' => 'array',
            'database_password' => 'encrypted',
        ];
    }

    /** @return HasMany<TenantDomainModel, $this> */
    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomainModel::class, 'tenant_id');
    }
}
