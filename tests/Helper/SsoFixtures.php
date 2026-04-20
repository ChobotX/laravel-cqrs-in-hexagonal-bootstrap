<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

final readonly class SsoFixtures
{
    public const string CONFIG_ID = '11111111-1111-1111-1111-111111111111';

    public const string USER_ID = '22222222-2222-2222-2222-222222222222';

    public const string IDENTITY_ID = '33333333-3333-3333-3333-333333333333';

    public const string OTHER_CONFIG_ID = '44444444-4444-4444-4444-444444444444';

    /** @param list<string> $allowedEmailDomains */
    public static function configuration(
        string $id = self::CONFIG_ID,
        ProviderType $providerType = ProviderType::Oidc,
        string $slug = 'primary',
        string $displayName = 'Primary OIDC',
        bool $enabled = true,
        bool $enforce = false,
        JitMode $jitMode = JitMode::InvitedOnly,
        array $allowedEmailDomains = [],
    ): SsoConfiguration {
        $now = new DateTimeImmutable('2026-01-01T00:00:00+00:00');

        return new SsoConfiguration(
            id: new SsoConfigurationId($id),
            providerType: $providerType,
            slug: $slug,
            displayName: $displayName,
            enabled: $enabled,
            enforce: $enforce,
            jitMode: $jitMode,
            allowedEmailDomains: new AllowedEmailDomains($allowedEmailDomains),
            config: ['client_id' => 'cid'],
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function identity(
        string $id = self::IDENTITY_ID,
        string $userId = self::USER_ID,
        string $configurationId = self::CONFIG_ID,
        string $subject = 'subject-1',
        string $email = 'user@example.com',
    ): UserSsoIdentity {
        return new UserSsoIdentity(
            id: new UserSsoIdentityId($id),
            userId: new UserId($userId),
            configurationId: new SsoConfigurationId($configurationId),
            subject: $subject,
            emailAtLink: $email,
            linkedAt: new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
        );
    }
}
