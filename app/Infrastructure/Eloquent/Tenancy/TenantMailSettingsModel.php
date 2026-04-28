<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Override;

final class TenantMailSettingsModel extends Model
{
    public const int SINGLETON_ID = 1;

    public $incrementing = false;

    public $timestamps = false;

    protected $connection = 'tenant';

    protected $keyType = 'int';

    protected $primaryKey = 'id';

    protected $table = 'tenant_mail_settings';

    protected $fillable = [
        'id',
        'provider',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
    ];

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'port' => 'integer',
            'password' => 'encrypted',
        ];
    }
}
