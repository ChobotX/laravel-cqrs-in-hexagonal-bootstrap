<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\UpdateTwoFactorSettingsCommand;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;
use App\Domain\User\Exception\InvalidTwoFactorPolicyException;
use App\Domain\User\Handler\Command\UpdateTwoFactorSettingsHandler;
use Tests\Helper\FakeTwoFactorSettingsRepository;

it('persists valid two-factor policy', function (): void {
    $repository = new FakeTwoFactorSettingsRepository(new TwoFactorSettings(false, true, true));
    $handler = new UpdateTwoFactorSettingsHandler($repository);

    $handler->handle(new UpdateTwoFactorSettingsCommand(
        requiredForAllUsers: true,
        emailOtpEnabled: true,
        totpEnabled: false,
    ));

    expect($repository->captured)->toBeInstanceOf(TwoFactorSettings::class)
        ->and($repository->captured?->requiredForAllUsers)->toBeTrue()
        ->and($repository->captured?->emailOtpEnabled)->toBeTrue()
        ->and($repository->captured?->totpEnabled)->toBeFalse();
});

it('throws when enforcement enabled with no methods enabled', function (): void {
    $repository = new FakeTwoFactorSettingsRepository(new TwoFactorSettings(false, true, true));
    $handler = new UpdateTwoFactorSettingsHandler($repository);

    $handler->handle(new UpdateTwoFactorSettingsCommand(
        requiredForAllUsers: true,
        emailOtpEnabled: false,
        totpEnabled: false,
    ));
})->throws(InvalidTwoFactorPolicyException::class);
