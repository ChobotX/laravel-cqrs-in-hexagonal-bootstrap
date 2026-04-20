<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Sso;

use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;

final readonly class UserSsoIdentityMapper
{
    public function toDomain(UserSsoIdentityModel $userSsoIdentityModel): UserSsoIdentity
    {
        return new UserSsoIdentity(
            id: new UserSsoIdentityId($userSsoIdentityModel->id),
            userId: new UserId($userSsoIdentityModel->user_id),
            configurationId: new SsoConfigurationId($userSsoIdentityModel->configuration_id),
            subject: $userSsoIdentityModel->subject,
            emailAtLink: $userSsoIdentityModel->email_at_link,
            linkedAt: $userSsoIdentityModel->linked_at->toDateTimeImmutable(),
        );
    }
}
