<?php

declare(strict_types=1);

namespace App\Application\Authorization;

use App\Domain\Authorization\Contract\Enum\AccessScope;

final readonly class AccessContext
{
    /**
     * @param  list<string>|null  $visibleIds  null = unrestricted (All scope)
     * @param  list<string>|null  $sharedResourceIds  null = sharing not applicable; list = resource IDs shared with the actor
     */
    public function __construct(
        public AccessScope $scope,
        public ?array $visibleIds,
        public ?array $sharedResourceIds = null,
    ) {}
}
