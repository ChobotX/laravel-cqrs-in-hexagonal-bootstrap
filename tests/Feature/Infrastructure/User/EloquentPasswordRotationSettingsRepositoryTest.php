<?php

declare(strict_types=1);

use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use Illuminate\Support\Facades\DB;

it('returns defaults when the singleton row is missing', function (): void {
    DB::connection('tenant')->table('password_rotation_settings')->where('id', 1)->delete();

    $passwordRotationSettings = app(PasswordRotationSettingsRepository::class)->get();

    expect($passwordRotationSettings->rotationEnabled)->toBeFalse()
        ->and($passwordRotationSettings->maxAgeDays)->toBeNull()
        ->and($passwordRotationSettings->historyCount)->toBe(5);
});

it('persists settings through save and reads them back', function (): void {
    $passwordRotationSettingsRepository = app(PasswordRotationSettingsRepository::class);

    $passwordRotationSettingsRepository->save(new PasswordRotationSettings(
        rotationEnabled: true,
        maxAgeDays: 120,
        historyCount: 9,
    ));

    $passwordRotationSettings = $passwordRotationSettingsRepository->get();

    expect($passwordRotationSettings->rotationEnabled)->toBeTrue()
        ->and($passwordRotationSettings->maxAgeDays)->toBe(120)
        ->and($passwordRotationSettings->historyCount)->toBe(9);
});
