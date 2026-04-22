<?php

declare(strict_types=1);

use App\Contract\Auth\AuthenticatedUser;
use App\Domain\User\Contract\Query\GetTwoFactorStatusQuery;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;
use App\Domain\User\Handler\Query\GetTwoFactorStatusHandler;
use Tests\Helper\FakeTwoFactorSettingsRepository;
use Tests\Helper\FakeUserTwoFactorStateRepository;

/**
 * @param  non-empty-string|null  $userId
 */
function twoFactorAuthenticatedUser(?string $userId): AuthenticatedUser
{
    return new readonly class($userId) implements AuthenticatedUser
    {
        public function __construct(private ?string $userId) {}

        public function id(): ?string
        {
            return $this->userId;
        }

        public function name(): ?string
        {
            return null;
        }

        public function impersonatorId(): ?string
        {
            return null;
        }

        public function isImpersonating(): bool
        {
            return false;
        }
    };
}

it('returns not required when policy disabled', function (): void {
    $handler = new GetTwoFactorStatusHandler(
        new FakeTwoFactorSettingsRepository(new TwoFactorSettings(false, true, true)),
        new FakeUserTwoFactorStateRepository,
        twoFactorAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $twoFactorUiStatus = $handler->handle(new GetTwoFactorStatusQuery);

    expect($twoFactorUiStatus->required)->toBeFalse()
        ->and($twoFactorUiStatus->emailOtpActive)->toBeFalse();
});

it('returns not required for guest context', function (): void {
    $handler = new GetTwoFactorStatusHandler(
        new FakeTwoFactorSettingsRepository(new TwoFactorSettings(true, true, true)),
        new FakeUserTwoFactorStateRepository,
        twoFactorAuthenticatedUser(null),
    );

    $twoFactorUiStatus = $handler->handle(new GetTwoFactorStatusQuery);

    expect($twoFactorUiStatus->required)->toBeFalse()
        ->and($twoFactorUiStatus->requiresSetup)->toBeFalse()
        ->and($twoFactorUiStatus->requiresChallenge)->toBeFalse()
        ->and($twoFactorUiStatus->emailOtpActive)->toBeFalse();
});

it('requires setup when tenant enforcement enabled and user has no method', function (): void {
    $handler = new GetTwoFactorStatusHandler(
        new FakeTwoFactorSettingsRepository(new TwoFactorSettings(true, true, true)),
        new FakeUserTwoFactorStateRepository,
        twoFactorAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $twoFactorUiStatus = $handler->handle(new GetTwoFactorStatusQuery);

    expect($twoFactorUiStatus->required)->toBeTrue()
        ->and($twoFactorUiStatus->requiresSetup)->toBeTrue()
        ->and($twoFactorUiStatus->requiresChallenge)->toBeFalse()
        ->and($twoFactorUiStatus->emailOtpActive)->toBeFalse();
});

it('requires challenge when enforcement enabled and user has confirmed method', function (): void {
    $stateRepository = new FakeUserTwoFactorStateRepository;
    $stateRepository->set(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserTwoFactorState(true, new DateTimeImmutable, null, null),
    );

    $handler = new GetTwoFactorStatusHandler(
        new FakeTwoFactorSettingsRepository(new TwoFactorSettings(true, true, true)),
        $stateRepository,
        twoFactorAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $twoFactorUiStatus = $handler->handle(new GetTwoFactorStatusQuery);

    expect($twoFactorUiStatus->required)->toBeTrue()
        ->and($twoFactorUiStatus->requiresSetup)->toBeFalse()
        ->and($twoFactorUiStatus->requiresChallenge)->toBeTrue()
        ->and($twoFactorUiStatus->emailOtpActive)->toBeTrue();
});

it('reflects user email otp off when not required and user disabled email', function (): void {
    $stateRepository = new FakeUserTwoFactorStateRepository;
    $stateRepository->set(
        new UserId('550e8400-e29b-41d4-a716-446655440000'),
        new UserTwoFactorState(false, null, null, null),
    );

    $handler = new GetTwoFactorStatusHandler(
        new FakeTwoFactorSettingsRepository(new TwoFactorSettings(false, true, true)),
        $stateRepository,
        twoFactorAuthenticatedUser('550e8400-e29b-41d4-a716-446655440000'),
    );

    $twoFactorUiStatus = $handler->handle(new GetTwoFactorStatusQuery);

    expect($twoFactorUiStatus->emailOtpActive)->toBeFalse();
});
