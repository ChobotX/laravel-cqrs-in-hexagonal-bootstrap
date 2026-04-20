<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Sso;

use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;

final readonly class EloquentUserSsoIdentityRepository implements UserSsoIdentityRepository
{
    public function __construct(
        private UserSsoIdentityMapper $userSsoIdentityMapper,
    ) {}

    public function findById(UserSsoIdentityId $userSsoIdentityId): ?UserSsoIdentity
    {
        $model = UserSsoIdentityModel::query()->find($userSsoIdentityId->value);

        return $model instanceof UserSsoIdentityModel ? $this->userSsoIdentityMapper->toDomain($model) : null;
    }

    public function findBySubject(SsoConfigurationId $ssoConfigurationId, string $subject): ?UserSsoIdentity
    {
        $model = UserSsoIdentityModel::query()
            ->where('configuration_id', $ssoConfigurationId->value)
            ->where('subject', $subject)
            ->first();

        return $model instanceof UserSsoIdentityModel ? $this->userSsoIdentityMapper->toDomain($model) : null;
    }

    public function listForUser(UserId $userId): array
    {
        return array_values(
            UserSsoIdentityModel::query()
                ->where('user_id', $userId->value)
                ->orderBy('linked_at')
                ->get()
                ->map(fn (UserSsoIdentityModel $userSsoIdentityModel): UserSsoIdentity => $this->userSsoIdentityMapper->toDomain($userSsoIdentityModel))
                ->all(),
        );
    }

    public function create(UserSsoIdentity $userSsoIdentity): void
    {
        $userSsoIdentityModel = new UserSsoIdentityModel;
        $userSsoIdentityModel->id = $userSsoIdentity->id->value;
        $userSsoIdentityModel->fill([
            'user_id' => $userSsoIdentity->userId->value,
            'configuration_id' => $userSsoIdentity->configurationId->value,
            'subject' => $userSsoIdentity->subject,
            'email_at_link' => $userSsoIdentity->emailAtLink,
            'linked_at' => $userSsoIdentity->linkedAt,
        ]);
        $userSsoIdentityModel->save();
    }

    public function delete(UserSsoIdentityId $userSsoIdentityId): void
    {
        UserSsoIdentityModel::query()->where('id', $userSsoIdentityId->value)->delete();
    }

    public function deleteAllForConfiguration(SsoConfigurationId $ssoConfigurationId): void
    {
        UserSsoIdentityModel::query()
            ->where('configuration_id', $ssoConfigurationId->value)
            ->delete();
    }
}
