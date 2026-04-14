<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Tenancy;

use App\Domain\Tenancy\Exception\TenantDisplayNameNotConfiguredException;
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
        'display_name',
        'logo_path',
        'display_timezone',
    ];

    public static function readDisplayName(): string
    {
        $value = self::query()->whereKey(self::SINGLETON_ID)->value('display_name');

        if (! is_string($value) || trim($value) === '') {
            throw new TenantDisplayNameNotConfiguredException;
        }

        return $value;
    }

    public static function readLogoPath(): ?string
    {
        $value = self::query()->whereKey(self::SINGLETON_ID)->value('logo_path');

        return is_string($value) && $value !== '' ? $value : null;
    }

    public static function readDisplayTimezone(): ?string
    {
        $value = self::query()->whereKey(self::SINGLETON_ID)->value('display_timezone');

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @param  array{display_name: string, logo_path: ?string, display_timezone: ?string}  $attributes
     */
    public static function writePreferences(array $attributes): void
    {
        self::query()->whereKey(self::SINGLETON_ID)->update($attributes);
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
