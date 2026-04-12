<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Entity;

use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;

/**
 * Immutable read-model snapshot of a User returned from queries in the User context.
 */
final readonly class User
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public UserId $id,
        /** Human-visible label or title. */
        public UserName $name,
        /** Email address used for lookup, delivery, or authentication flows. */
        public Email $email,
        /** Boolean flag for this state or capability. */
        public bool $isActivated = false,
        /** Optional avatarFile identifier when absent. */
        public ?FileId $avatarFileId = null,
    ) {}
}
