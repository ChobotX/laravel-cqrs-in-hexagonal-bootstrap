<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Query\GetTwoFactorStatusQuery;
use App\Domain\User\Contract\Repository\TwoFactorSettingsRepository;
use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Domain\User\Contract\ValueObject\TwoFactorUiStatus;
use App\Domain\User\Contract\ValueObject\UserId;

/** @implements QueryHandler<GetTwoFactorStatusQuery, TwoFactorUiStatus> */
final readonly class GetTwoFactorStatusHandler implements QueryHandler
{
    public function __construct(
        private TwoFactorSettingsRepository $twoFactorSettingsRepository,
        private UserTwoFactorStateRepository $userTwoFactorStateRepository,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function handle(Query $query): TwoFactorUiStatus
    {
        $authenticatedUserId = $this->authenticatedUser->id();

        if ($authenticatedUserId === null) {
            return new TwoFactorUiStatus(false, false, false, false, false, false);
        }

        $userId = new UserId($authenticatedUserId);
        $userTwoFactorState = $this->userTwoFactorStateRepository->get($userId);
        $emailOtpActive = $userTwoFactorState->emailEnabled;

        $twoFactorSettings = $this->twoFactorSettingsRepository->get();

        if (! $twoFactorSettings->requiredForAllUsers) {
            return new TwoFactorUiStatus(
                false,
                false,
                false,
                $twoFactorSettings->emailOtpEnabled,
                $twoFactorSettings->totpEnabled,
                $emailOtpActive,
            );
        }

        $requiresSetup = ! $userTwoFactorState->hasConfirmedMethod();

        return new TwoFactorUiStatus(
            required: true,
            requiresSetup: $requiresSetup,
            requiresChallenge: ! $requiresSetup,
            emailAllowed: $twoFactorSettings->emailOtpEnabled,
            totpAllowed: $twoFactorSettings->totpEnabled,
            emailOtpActive: $emailOtpActive,
        );
    }
}
