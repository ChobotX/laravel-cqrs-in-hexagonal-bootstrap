<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Repository;

use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;

interface UserTwoFactorStateRepository
{
    public function get(UserId $userId): UserTwoFactorState;

    public function save(UserId $userId, UserTwoFactorState $userTwoFactorState): void;
}
