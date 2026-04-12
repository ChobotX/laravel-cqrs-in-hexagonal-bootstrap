<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for share record in the Authorization bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Record sharing is enforced per-resource by the handler')]
final readonly class ShareRecordCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $granteeUserId,
        /** Classifier string or type discriminator. */
        public string $resourceType,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $resourceId,
        /** Field `action` for this contract; see module docs for validation rules. */
        public string $action,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $grantorUserId,
    ) {}
}
