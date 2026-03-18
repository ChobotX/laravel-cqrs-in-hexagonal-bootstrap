<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\RevokeRecordShare;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Record share revocation is enforced per-resource by the handler')]
final readonly class RevokeRecordShareCommand implements Command
{
    public function __construct(
        public string $granteeUserId,
        public string $resourceType,
        public string $resourceId,
    ) {}
}
