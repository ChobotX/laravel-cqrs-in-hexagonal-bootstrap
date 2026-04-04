<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Command\UpdateNotificationPreferences;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users update only their own notification preferences')]
final readonly class UpdateNotificationPreferencesCommand implements Command
{
    /**
     * @param  array<string, list<string>>  $preferences  level => channels
     */
    public function __construct(
        public string $userId,
        public array $preferences,
    ) {}
}
