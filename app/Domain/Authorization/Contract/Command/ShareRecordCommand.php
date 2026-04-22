<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for share record in the Authorization bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Record sharing is enforced per-resource by the handler')]
final readonly class ShareRecordCommand implements Command
{
    /** @param list<string> $actions */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $granteeUserId,
        /** Classifier string or type discriminator. */
        public string $resourceType,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $resourceId,
        /** Action names to grant (values of the Action enum). */
        public array $actions,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $grantorUserId,
    ) {}
}
