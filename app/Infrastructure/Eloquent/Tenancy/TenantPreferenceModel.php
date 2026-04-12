<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Override;

final class TenantPreferenceModel extends Model
{
    public const int SINGLETON_ID = 1;

    public $incrementing = false;

    public $timestamps = false;

    protected $connection = 'tenant';

    protected $keyType = 'int';

    protected $primaryKey = 'id';

    protected $table = 'tenant_preferences';

    protected $fillable = [
        'id',
        'display_timezone',
    ];

    public static function readDisplayTimezone(): ?string
    {
        $value = self::query()->whereKey(self::SINGLETON_ID)->value('display_timezone');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public static function writeDisplayTimezone(?string $displayTimezone): void
    {
        self::query()->updateOrInsert(
            ['id' => self::SINGLETON_ID],
            ['display_timezone' => $displayTimezone],
        );
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }
}
