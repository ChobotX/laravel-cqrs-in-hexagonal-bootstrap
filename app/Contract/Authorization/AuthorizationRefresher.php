<?php

declare(strict_types=1);

namespace App\Contract\Authorization;

interface AuthorizationRefresher
{
    public function refreshForUser(string $userId): void;
}
