<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

/**
 * Domain service contract for access decision in the Authorization bounded context.
 */
interface AccessDecision
{
    /** Contract operation `granted`; see infrastructure for behavior. */
    public function granted(): bool;

    /** Contract operation `scope`; see infrastructure for behavior. */
    public function scope(): string;
}
