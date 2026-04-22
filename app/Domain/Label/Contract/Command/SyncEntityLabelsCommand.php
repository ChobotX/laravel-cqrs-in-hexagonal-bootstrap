<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for sync entity labels in the Label bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Null-safe — null input means skip, controller decides based on form visibility')]
final readonly class SyncEntityLabelsCommand implements Command
{
    /**
     * @param  list<string>|null  $submittedLabelIds  null = skip sync, [] = remove all
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $entityId,
        /** Classifier string or type discriminator. */
        public string $entityType,
        /** List of stable identifiers for batch operations. */
        public ?array $submittedLabelIds,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $actingUserId,
    ) {}
}
