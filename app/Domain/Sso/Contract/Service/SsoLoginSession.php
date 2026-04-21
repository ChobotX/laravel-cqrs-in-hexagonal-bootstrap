<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Service;

/**
 * Bridges the LoginViaSsoCommand handler and the presentation controller across a
 * single SSO login flow:
 *
 * - `remember*Handshake` stores the CSRF `state` + OIDC `nonce` before redirect.
 * - `consume*Handshake` returns the stored nonce (and removes the entry) when the
 *   IdP callback matches `state`; a miss means replay/CSRF attempt.
 * - `setLastResolvedUserId` / `pullLastResolvedUserId` carry the just-authenticated
 *   user id from the LoginViaSsoCommand handler back to the controller so it can
 *   establish the HTTP session via Guard.
 *
 * Implementations are request-scoped (Laravel session-backed in production).
 */
interface SsoLoginSession
{
    public function rememberHandshake(string $slug, string $state, ?string $nonce = null): void;

    /** Consume the state for `$slug`; returns the stored nonce when `$state` matches, null otherwise. */
    public function consumeHandshake(string $slug, string $state): ?string;

    public function setLastResolvedUserId(string $userId): void;

    public function pullLastResolvedUserId(): ?string;

    public function clear(): void;
}
