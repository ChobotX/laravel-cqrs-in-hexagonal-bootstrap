<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Authorization\Contract\Service\AuthorizationRefresher;

final class FakeAuthorizationRefresher implements AuthorizationRefresher
{
    /** @var list<string> */
    public array $refreshedUserIds = [];

    public function refreshForUser(string $userId): void
    {
        $this->refreshedUserIds[] = $userId;
    }
}
