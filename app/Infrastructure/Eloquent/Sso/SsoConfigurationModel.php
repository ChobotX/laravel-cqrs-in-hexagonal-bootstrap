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
 * @property string $provider_type
 * @property string $slug
 * @property string $display_name
 * @property bool $enabled
 * @property bool $enforce
 * @property string $jit_mode
 * @property list<string> $allowed_email_domains
 * @property array<string, scalar|array<int|string, mixed>|null> $config
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[HardDelete(reason: 'Tenant-scoped configuration; admin-initiated deletion cascades to identities.')]
final class SsoConfigurationModel extends Model
{
    use HasOptimisticLocking;
    use HasUuids;

    public $incrementing = false;

    protected $table = 'sso_configurations';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'provider_type',
        'slug',
        'display_name',
        'enabled',
        'enforce',
        'jit_mode',
        'allowed_email_domains',
        'config',
    ];

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'enforce' => 'boolean',
            'allowed_email_domains' => 'array',
            'config' => 'encrypted:array',
        ];
    }
}
