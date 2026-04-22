<?php

declare(strict_types=1);

namespace App\Contract\Auth;

/**
 * Ambient actor context for the current request/command — identity, display, and impersonation state.
 */
interface AuthenticatedUser
{
    /** Stable id of the actor, or null when unauthenticated. */
    public function id(): ?string;

    /** Display name of the actor, or null when unauthenticated. */
    public function name(): ?string;

    /** Impersonator id if the current session is impersonating, else null. */
    public function impersonatorId(): ?string;

    public function isImpersonating(): bool;
}
