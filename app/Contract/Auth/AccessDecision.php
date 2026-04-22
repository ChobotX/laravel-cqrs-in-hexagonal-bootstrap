<?php

declare(strict_types=1);

namespace App\Contract\Auth;

/**
 * Result of a scope-aware permission check: whether access is granted and at which scope.
 */
interface AccessDecision
{
    public function granted(): bool;

    public function scope(): string;
}
