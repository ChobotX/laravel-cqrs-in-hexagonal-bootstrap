<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class EloquentPasswordRotationSettingsRepository implements PasswordRotationSettingsRepository
{
    private const int SINGLETON_ID = 1;

    public function get(): PasswordRotationSettings
    {
        $row = DB::connection('tenant')->table('password_rotation_settings')->where('id', self::SINGLETON_ID)->first();

        if (! $row instanceof stdClass) {
            return new PasswordRotationSettings(
                rotationEnabled: false,
                maxAgeDays: null,
                historyCount: PasswordRotationSettings::DEFAULT_HISTORY_COUNT,
            );
        }

        $maxAgeRaw = $row->max_age_days;
        $historyRaw = $row->history_count;

        return new PasswordRotationSettings(
            rotationEnabled: (bool) $row->rotation_enabled,
            maxAgeDays: $maxAgeRaw !== null && is_numeric($maxAgeRaw) ? (int) $maxAgeRaw : null,
            historyCount: is_numeric($historyRaw) ? (int) $historyRaw : PasswordRotationSettings::DEFAULT_HISTORY_COUNT,
        );
    }

    public function save(PasswordRotationSettings $passwordRotationSettings): void
    {
        DB::connection('tenant')->table('password_rotation_settings')->where('id', self::SINGLETON_ID)->update([
            'rotation_enabled' => $passwordRotationSettings->rotationEnabled,
            'max_age_days' => $passwordRotationSettings->maxAgeDays,
            'history_count' => $passwordRotationSettings->historyCount,
        ]);
    }
}
