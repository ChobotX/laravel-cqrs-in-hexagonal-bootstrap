<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Service;

interface RecipientResolver
{
    /** @return list<string> */
    public function resolveTeamMembers(string $teamId): array;

    /** @return list<string> */
    public function resolveTeamWithSubteamMembers(string $teamId): array;
}
