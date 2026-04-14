<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;

final class FakeUserTwoFactorStateRepository implements UserTwoFactorStateRepository
{
    public ?UserTwoFactorState $captured = null;

    /** @var array<string, UserTwoFactorState> */
    private array $states = [];

    public function set(UserId $userId, UserTwoFactorState $userTwoFactorState): void
    {
        $this->states[$userId->value] = $userTwoFactorState;
    }

    public function get(UserId $userId): UserTwoFactorState
    {
        return $this->states[$userId->value] ?? new UserTwoFactorState(false, null, null, null);
    }

    public function save(UserId $userId, UserTwoFactorState $userTwoFactorState): void
    {
        $this->captured = $userTwoFactorState;
        $this->states[$userId->value] = $userTwoFactorState;
    }
}
