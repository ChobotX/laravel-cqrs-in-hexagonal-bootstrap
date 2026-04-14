<?php

declare(strict_types=1);

use App\Domain\User\Contract\ValueObject\TwoFactorSettings;
use App\Infrastructure\User\EloquentTwoFactorSettingsRepository;
use Illuminate\Support\Facades\DB;

it('reads and saves tenant two-factor settings', function (): void {
    $repository = new EloquentTwoFactorSettingsRepository;

    $repository->save(new TwoFactorSettings(
        requiredForAllUsers: true,
        emailOtpEnabled: true,
        totpEnabled: false,
    ));

    $twoFactorSettings = $repository->get();

    expect($twoFactorSettings->requiredForAllUsers)->toBeTrue()
        ->and($twoFactorSettings->emailOtpEnabled)->toBeTrue()
        ->and($twoFactorSettings->totpEnabled)->toBeFalse();
});

it('returns defaults when singleton row is missing', function (): void {
    DB::connection('tenant')->table('two_factor_settings')->where('id', 1)->delete();

    $repository = new EloquentTwoFactorSettingsRepository;
    $twoFactorSettings = $repository->get();

    expect($twoFactorSettings->requiredForAllUsers)->toBeFalse()
        ->and($twoFactorSettings->emailOtpEnabled)->toBeTrue()
        ->and($twoFactorSettings->totpEnabled)->toBeTrue();

    DB::connection('tenant')->table('two_factor_settings')->insert([
        'id' => 1,
        'required_for_all_users' => false,
        'email_otp_enabled' => true,
        'totp_enabled' => true,
    ]);
});
