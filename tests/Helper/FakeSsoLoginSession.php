<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Sso\Contract\Service\SsoLoginSession;

final class FakeSsoLoginSession implements SsoLoginSession
{
    public ?string $lastUserId = null;

    public function setLastResolvedUserId(string $userId): void
    {
        $this->lastUserId = $userId;
    }

    public function pullLastResolvedUserId(): ?string
    {
        $value = $this->lastUserId;
        $this->lastUserId = null;

        return $value;
    }
}
