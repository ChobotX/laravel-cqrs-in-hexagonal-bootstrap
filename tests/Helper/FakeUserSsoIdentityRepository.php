<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;

final class FakeUserSsoIdentityRepository implements UserSsoIdentityRepository
{
    /** @var list<UserSsoIdentity> */
    public array $created = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @var list<string> */
    public array $bulkDeletedConfigurationIds = [];

    /** @param array<string, UserSsoIdentity> $items */
    public function __construct(
        private array $items = [],
    ) {}

    public function findById(UserSsoIdentityId $userSsoIdentityId): ?UserSsoIdentity
    {
        return $this->items[$userSsoIdentityId->value] ?? null;
    }

    public function findBySubject(SsoConfigurationId $ssoConfigurationId, string $subject): ?UserSsoIdentity
    {
        foreach ($this->items as $item) {
            if ($item->configurationId->equals($ssoConfigurationId) && $item->subject === $subject) {
                return $item;
            }
        }

        return null;
    }

    public function listForUser(UserId $userId): array
    {
        return array_values(array_filter($this->items, fn (UserSsoIdentity $userSsoIdentity): bool => $userSsoIdentity->userId->equals($userId)));
    }

    public function create(UserSsoIdentity $userSsoIdentity): void
    {
        $this->created[] = $userSsoIdentity;
        $this->items[$userSsoIdentity->id->value] = $userSsoIdentity;
    }

    public function delete(UserSsoIdentityId $userSsoIdentityId): void
    {
        $this->deleted[] = $userSsoIdentityId->value;
        unset($this->items[$userSsoIdentityId->value]);
    }

    public function deleteAllForConfiguration(SsoConfigurationId $ssoConfigurationId): void
    {
        $this->bulkDeletedConfigurationIds[] = $ssoConfigurationId->value;

        foreach ($this->items as $key => $identity) {
            if ($identity->configurationId->equals($ssoConfigurationId)) {
                unset($this->items[$key]);
            }
        }
    }
}
