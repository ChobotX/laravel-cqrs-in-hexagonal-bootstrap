<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Tenancy;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

final class TenantDomainModel extends Model
{
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
