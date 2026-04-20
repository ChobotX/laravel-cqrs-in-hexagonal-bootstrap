<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Service;

/**
 * Carries the just-resolved user id between the LoginViaSsoCommand handler and
 * the presentation controller that establishes the HTTP session.
 *
 * Implementations are request-scoped (Laravel session-backed in production).
 */
interface SsoLoginSession
{
    public function setLastResolvedUserId(string $userId): void;

    public function pullLastResolvedUserId(): ?string;
}
