<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Service;

/**
 * Domain service contract for recipient resolver in the Notification bounded context.
 */
interface RecipientResolver
{
    /** @return list<string> */
    public function resolveTeamMembers(string $teamId): array;

    /** @return list<string> */
    public function resolveTeamWithSubteamMembers(string $teamId): array;
}
