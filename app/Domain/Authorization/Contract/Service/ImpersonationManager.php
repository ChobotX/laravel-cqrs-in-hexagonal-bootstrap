<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

/**
 * Domain service contract for impersonation manager in the Authorization bounded context.
 */
interface ImpersonationManager
{
    /** Contract operation `start`; see infrastructure for behavior. */
    public function start(string $impersonatorId, string $targetUserId): void;

    /** Contract operation `stop`; see infrastructure for behavior. */
    public function stop(): void;

    /** Evaluates the rule without mutating domain state. */
    public function isActive(): bool;

    /** Contract operation `realUserId`; see infrastructure for behavior. */
    public function realUserId(): ?string;

    /** Contract operation `impersonatedUserId`; see infrastructure for behavior. */
    public function impersonatedUserId(): ?string;
}
