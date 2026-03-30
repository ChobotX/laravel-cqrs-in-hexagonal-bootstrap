<?php

declare(strict_types=1);

namespace App\Application\Authorization;

use App\Domain\Authorization\AccessScope;

final readonly class AccessContext
{
    /** @param list<string>|null $visibleIds null = unrestricted (All scope) */
    public function __construct(
        public AccessScope $scope,
        public ?array $visibleIds,
    ) {}
}
