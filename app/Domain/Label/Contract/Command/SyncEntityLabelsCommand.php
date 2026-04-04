<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Null-safe — null input means skip, controller decides based on form visibility')]
final readonly class SyncEntityLabelsCommand implements Command
{
    /**
     * @param  list<string>|null  $submittedLabelIds  null = skip sync, [] = remove all
     */
    public function __construct(
        public string $entityId,
        public string $entityType,
        public ?array $submittedLabelIds,
        public string $actingUserId,
    ) {}
}
