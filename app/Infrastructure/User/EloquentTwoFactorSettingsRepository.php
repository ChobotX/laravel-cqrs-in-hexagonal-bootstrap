<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Repository\TwoFactorSettingsRepository;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class EloquentTwoFactorSettingsRepository implements TwoFactorSettingsRepository
{
    private const int SINGLETON_ID = 1;

    public function get(): TwoFactorSettings
    {
        $row = DB::connection('tenant')->table('two_factor_settings')->where('id', self::SINGLETON_ID)->first();

        if (! $row instanceof stdClass) {
            return new TwoFactorSettings(requiredForAllUsers: false, emailOtpEnabled: true, totpEnabled: true);
        }

        return new TwoFactorSettings(
            requiredForAllUsers: (bool) $row->required_for_all_users,
            emailOtpEnabled: (bool) $row->email_otp_enabled,
            totpEnabled: (bool) $row->totp_enabled,
        );
    }

    public function save(TwoFactorSettings $twoFactorSettings): void
    {
        DB::connection('tenant')->table('two_factor_settings')->where('id', self::SINGLETON_ID)->update([
            'required_for_all_users' => $twoFactorSettings->requiredForAllUsers,
            'email_otp_enabled' => $twoFactorSettings->emailOtpEnabled,
            'totp_enabled' => $twoFactorSettings->totpEnabled,
        ]);
    }
}
