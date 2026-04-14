<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Repository;

use App\Domain\User\Contract\ValueObject\EmailTwoFactorChallenge;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

interface EmailTwoFactorChallengeRepository
{
    public function issue(UserId $userId, string $codeHash, DateTimeImmutable $expiresAt): void;

    public function latest(UserId $userId): ?EmailTwoFactorChallenge;

    public function markAttempt(UserId $userId): void;

    public function consume(UserId $userId): void;

    public function deleteAllForUser(UserId $userId): void;
}
