<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Entity;

use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/**
 * Link between a domain `User` and an external IdP subject for one SsoConfiguration.
 */
final readonly class UserSsoIdentity
{
    public function __construct(
        /** Stable identifier of this link row. */
        public UserSsoIdentityId $id,
        /** User this identity belongs to. */
        public UserId $userId,
        /** Configuration this subject was issued under. */
        public SsoConfigurationId $configurationId,
        /** IdP subject identifier (NOT email). */
        public string $subject,
        /** Email value at the time of linking, kept for audit only. */
        public string $emailAtLink,
        /** When the link row was created. */
        public DateTimeImmutable $linkedAt,
    ) {}
}
