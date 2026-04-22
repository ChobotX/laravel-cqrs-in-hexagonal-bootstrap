<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Auth\ImpersonationManager;

final class FakeImpersonationManager implements ImpersonationManager
{
    public bool $started = false;

    public bool $stopped = false;

    private ?string $impersonatorId = null;

    private ?string $impersonatedUserId = null;

    public function start(string $impersonatorId, string $targetUserId): void
    {
        $this->impersonatorId = $impersonatorId;
        $this->impersonatedUserId = $targetUserId;
        $this->started = true;
    }

    public function stop(): void
    {
        $this->impersonatorId = null;
        $this->impersonatedUserId = null;
        $this->stopped = true;
    }

    public function isActive(): bool
    {
        return $this->impersonatorId !== null;
    }

    public function realUserId(): ?string
    {
        return $this->impersonatorId;
    }

    public function impersonatedUserId(): ?string
    {
        return $this->impersonatedUserId;
    }
}
