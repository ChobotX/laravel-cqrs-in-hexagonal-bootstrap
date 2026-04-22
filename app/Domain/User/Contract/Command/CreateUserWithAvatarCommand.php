<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;
use App\Domain\File\Contract\ValueObject\FileUpload;

/**
 * Orchestrates avatar storage and user creation via one controller dispatch.
 * Handler fans out to {@see \App\Domain\File\Contract\Command\StoreAvatarCommand}
 * (when an upload is present) and {@see CreateUserCommand}.
 */
#[RequiresPermission('users.list.create')]
final readonly class CreateUserWithAvatarCommand implements Command
{
    public function __construct(
        /** Stable identifier for the user being created. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $email,
        /** Actor performing the upload; recorded on the stored avatar file. */
        public string $uploadedBy,
        /** Optional avatar upload; when null no avatar is stored. */
        public ?FileUpload $avatarUpload = null,
    ) {}
}
