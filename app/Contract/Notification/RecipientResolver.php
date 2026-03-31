<?php

declare(strict_types=1);

namespace App\Contract\Notification;

interface RecipientResolver
{
    /** @return list<string> */
    public function resolveTeamMembers(string $teamId): array;

    /** @return list<string> */
    public function resolveTeamWithSubteamMembers(string $teamId): array;
}
