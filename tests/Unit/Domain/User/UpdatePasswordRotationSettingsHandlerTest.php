<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\UpdatePasswordRotationSettingsCommand;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Domain\User\Exception\InvalidPasswordRotationPolicyException;
use App\Domain\User\Handler\Command\UpdatePasswordRotationSettingsHandler;
use Tests\Helper\FakePasswordRotationSettingsRepository;

it('persists valid settings', function (): void {
    $repository = new FakePasswordRotationSettingsRepository(
        new PasswordRotationSettings(rotationEnabled: false, maxAgeDays: null, historyCount: 5),
    );

    $handler = new UpdatePasswordRotationSettingsHandler($repository);

    $handler->handle(new UpdatePasswordRotationSettingsCommand(
        rotationEnabled: true,
        maxAgeDays: 90,
        historyCount: 12,
    ));

    $captured = $repository->captured;
    assert($captured instanceof PasswordRotationSettings);

    expect($captured->rotationEnabled)->toBeTrue()
        ->and($captured->maxAgeDays)->toBe(90)
        ->and($captured->historyCount)->toBe(12);
});

it('throws when rotation enabled and max age is out of range', function (): void {
    $repository = new FakePasswordRotationSettingsRepository(
        new PasswordRotationSettings(rotationEnabled: false, maxAgeDays: null, historyCount: 5),
    );

    $handler = new UpdatePasswordRotationSettingsHandler($repository);

    $handler->handle(new UpdatePasswordRotationSettingsCommand(
        rotationEnabled: true,
        maxAgeDays: 4000,
        historyCount: 5,
    ));
})->throws(InvalidPasswordRotationPolicyException::class);

it('throws when rotation enabled and max age is null', function (): void {
    $repository = new FakePasswordRotationSettingsRepository(
        new PasswordRotationSettings(rotationEnabled: false, maxAgeDays: null, historyCount: 5),
    );

    $handler = new UpdatePasswordRotationSettingsHandler($repository);

    $handler->handle(new UpdatePasswordRotationSettingsCommand(
        rotationEnabled: true,
        maxAgeDays: null,
        historyCount: 5,
    ));
})->throws(InvalidPasswordRotationPolicyException::class);

it('throws when history count is out of range', function (): void {
    $repository = new FakePasswordRotationSettingsRepository(
        new PasswordRotationSettings(rotationEnabled: false, maxAgeDays: null, historyCount: 5),
    );

    $handler = new UpdatePasswordRotationSettingsHandler($repository);

    $handler->handle(new UpdatePasswordRotationSettingsCommand(
        rotationEnabled: false,
        maxAgeDays: null,
        historyCount: 0,
    ));
})->throws(InvalidPasswordRotationPolicyException::class);
