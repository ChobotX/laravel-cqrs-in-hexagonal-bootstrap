<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Repository;

use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;

interface UserSsoIdentityRepository
{
    public function findById(UserSsoIdentityId $userSsoIdentityId): ?UserSsoIdentity;

    public function findBySubject(SsoConfigurationId $ssoConfigurationId, string $subject): ?UserSsoIdentity;

    /** @return list<UserSsoIdentity> */
    public function listForUser(UserId $userId): array;

    public function create(UserSsoIdentity $userSsoIdentity): void;

    public function delete(UserSsoIdentityId $userSsoIdentityId): void;

    public function deleteAllForConfiguration(SsoConfigurationId $ssoConfigurationId): void;
}
