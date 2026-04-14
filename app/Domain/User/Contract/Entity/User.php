<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Entity;

use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
use DateTimeImmutable;

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
        /** When password last changed; null if never set. */
        public ?DateTimeImmutable $passwordChangedAt = null,
        /** Whether email OTP is enabled for this user. */
        public bool $emailTwoFactorEnabled = false,
        /** When email OTP method was confirmed. */
        public ?DateTimeImmutable $emailTwoFactorConfirmedAt = null,
        /** Shared secret for TOTP method, null when not initialized. */
        public ?string $totpSecret = null,
        /** When TOTP method was confirmed. */
        public ?DateTimeImmutable $totpConfirmedAt = null,
    ) {}
}
