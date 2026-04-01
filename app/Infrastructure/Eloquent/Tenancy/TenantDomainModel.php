<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Tenancy;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[HardDelete(reason: 'Tenant domain config, managed by provisioner, no audit trail needed')]
final class TenantDomainModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $connection = 'landlord';

    protected $table = 'tenant_domains';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tenant_id',
        'domain',
        'is_primary',
    ];

    /** @return BelongsTo<TenantModel, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(TenantModel::class, 'tenant_id');
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
