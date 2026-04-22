<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetPasswordRotationStatusQuery;
use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Domain\User\Contract\ValueObject\PasswordRotationUiStatus;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements QueryHandler<GetPasswordRotationStatusQuery, PasswordRotationUiStatus> */
final readonly class GetPasswordRotationStatusHandler implements QueryHandler
{
    private const int SECONDS_PER_DAY = 86400;

    private const float WARNING_WINDOW_RATIO = 0.1;

    public function __construct(
        private PasswordRotationSettingsRepository $passwordRotationSettingsRepository,
        private UserRepository $userRepository,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function handle(Query $query): PasswordRotationUiStatus
    {
        $authenticatedUserId = $this->authenticatedUser->id();

        if ($authenticatedUserId === null) {
            return new PasswordRotationUiStatus(PasswordRotationUiStatus::OK);
        }

        $passwordRotationSettings = $this->passwordRotationSettingsRepository->get();

        if (! $passwordRotationSettings->rotationEnabled || $passwordRotationSettings->maxAgeDays === null || $passwordRotationSettings->maxAgeDays < PasswordRotationSettings::MIN_PASSWORD_AGE_DAYS) {
            return new PasswordRotationUiStatus(PasswordRotationUiStatus::OK);
        }

        $user = $this->userRepository->findById(new UserId($authenticatedUserId));

        if (! $user instanceof User || ! $user->passwordChangedAt instanceof DateTimeImmutable) {
            return new PasswordRotationUiStatus(PasswordRotationUiStatus::EXPIRED);
        }

        $expiresAt = $user->passwordChangedAt->modify('+'.$passwordRotationSettings->maxAgeDays.' days');
        $now = new DateTimeImmutable;

        if ($now > $expiresAt) {
            return new PasswordRotationUiStatus(PasswordRotationUiStatus::EXPIRED, $expiresAt);
        }

        $totalSeconds = $passwordRotationSettings->maxAgeDays * self::SECONDS_PER_DAY;
        $warningSeconds = (int) max(1, round($totalSeconds * self::WARNING_WINDOW_RATIO));
        $warningStartsAt = $expiresAt->getTimestamp() - $warningSeconds;

        if ($now->getTimestamp() >= $warningStartsAt) {
            return new PasswordRotationUiStatus(PasswordRotationUiStatus::WARNING, $expiresAt);
        }

        return new PasswordRotationUiStatus(PasswordRotationUiStatus::OK);
    }
}
