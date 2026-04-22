<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for revoke record share in the Authorization bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Record share revocation is enforced per-resource by the handler')]
final readonly class RevokeRecordShareCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $granteeUserId,
        /** Classifier string or type discriminator. */
        public string $resourceType,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $resourceId,
    ) {}
}
