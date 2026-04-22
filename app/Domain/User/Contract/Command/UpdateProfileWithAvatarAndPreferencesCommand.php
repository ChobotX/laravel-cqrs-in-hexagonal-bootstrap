<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\Sensitive;
use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;
use App\Domain\File\Contract\ValueObject\FileUpload;

/**
 * Orchestrates avatar storage, profile update, and notification preference sync via one dispatch.
 * Handler fans out to {@see \App\Domain\File\Contract\Command\StoreAvatarCommand},
 * {@see UpdateProfileCommand} and
 * {@see \App\Domain\Notification\Contract\Command\UpdateNotificationPreferencesCommand}.
 */
#[SkipPermissionCheck(reason: 'Profile self-edit is available to all authenticated users')]
final readonly class UpdateProfileWithAvatarAndPreferencesCommand implements Command
{
    /**
     * @param  array<string, list<string>>|null  $notificationPreferences  null = skip, level => channels otherwise
     */
    public function __construct(
        /** Authenticated user whose profile is being updated. */
        public string $userId,
        /** Human-visible label or title. */
        public string $name,
        /** Email or null when not submitted. */
        public ?string $email = null,
        #[Sensitive]
        /** Raw password or null when not submitted. */
        public ?string $rawPassword = null,
        /** Optional avatar upload; ignored when $removeAvatar is true. */
        public ?FileUpload $avatarUpload = null,
        /** When true, clears the current avatar (takes precedence over $avatarUpload). */
        public bool $removeAvatar = false,
        public ?array $notificationPreferences = null,
    ) {}
}
