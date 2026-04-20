<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Sso;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;

final readonly class SsoConfigurationMapper
{
    public function toDomain(SsoConfigurationModel $ssoConfigurationModel): SsoConfiguration
    {
        /** @var list<string> $domains */
        $domains = $ssoConfigurationModel->allowed_email_domains ?? [];
        /** @var array<string, scalar|array<int|string, mixed>|null> $config */
        $config = $ssoConfigurationModel->config ?? [];

        return new SsoConfiguration(
            id: new SsoConfigurationId($ssoConfigurationModel->id),
            providerType: ProviderType::from($ssoConfigurationModel->provider_type),
            slug: $ssoConfigurationModel->slug,
            displayName: $ssoConfigurationModel->display_name,
            enabled: $ssoConfigurationModel->enabled,
            enforce: $ssoConfigurationModel->enforce,
            jitMode: JitMode::from($ssoConfigurationModel->jit_mode),
            allowedEmailDomains: new AllowedEmailDomains($domains),
            config: $config,
            createdAt: $ssoConfigurationModel->created_at->toDateTimeImmutable(),
            updatedAt: $ssoConfigurationModel->updated_at->toDateTimeImmutable(),
        );
    }
}
