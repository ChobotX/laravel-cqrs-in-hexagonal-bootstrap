<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Tenancy;

use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

final class TenantModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $connection = 'landlord';

    protected $table = 'tenants';

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

    /** @return HasMany<TenantDomainModel, $this> */
    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomainModel::class, 'tenant_id');
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'config' => 'array',
            'database_password' => 'encrypted',
        ];
    }
}
