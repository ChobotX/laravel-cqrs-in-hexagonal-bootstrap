<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

/**
 * Domain service contract for invite link in the User bounded context.
 */
interface InviteLinkGenerator
{
    /** Creates an opaque identifier or signed value. */
    public function generate(string $userId): string;
}
