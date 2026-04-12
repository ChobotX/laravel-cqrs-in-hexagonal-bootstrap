<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Command payload for update notification preferences in the Notification bounded context; dispatched through the command bus.
 */
#[SkipPermissionCheck(reason: 'Users update only their own notification preferences')]
final readonly class UpdateNotificationPreferencesCommand implements Command
{
    /**
     * @param  array<string, list<string>>  $preferences  level => channels
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Array for `preferences`; see constructor PHPDoc for structural tags when present. */
        public array $preferences,
    ) {}
}
