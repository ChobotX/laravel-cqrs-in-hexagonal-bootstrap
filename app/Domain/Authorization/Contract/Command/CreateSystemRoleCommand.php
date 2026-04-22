<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Bootstrap-only: creates a system-marked role during tenant initialization. Not dispatched
 * from user-facing flows — management roles use {@see CreateRoleCommand}.
 */
#[SkipPermissionCheck(reason: 'Dispatched internally during tenant bootstrap before any user exists')]
final readonly class CreateSystemRoleCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
        /** Longer human-readable explanation for admin UI or emails. */
        public string $description,
    ) {}
}
