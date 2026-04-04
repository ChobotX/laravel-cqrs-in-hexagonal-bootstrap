<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

interface AuthorizationRefresher
{
    public function refreshForUser(string $userId): void;
}
