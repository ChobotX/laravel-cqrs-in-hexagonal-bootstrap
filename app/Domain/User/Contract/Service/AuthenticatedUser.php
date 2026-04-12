<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

/**
 * Domain service contract for authenticated user in the User bounded context.
 */
interface AuthenticatedUser
{
    /** Contract operation `id`; see infrastructure for behavior. */
    public function id(): ?string;

    /** Contract operation `name`; see infrastructure for behavior. */
    public function name(): ?string;

    /** Contract operation `impersonatorId`; see infrastructure for behavior. */
    public function impersonatorId(): ?string;

    /** Evaluates the rule without mutating domain state. */
    public function isImpersonating(): bool;
}
