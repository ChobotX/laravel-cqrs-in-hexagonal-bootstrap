<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Service\SsoAuthenticator;
use App\Domain\Sso\Contract\Service\SsoAuthenticatorRegistry as RegistryContract;

final readonly class SsoAuthenticatorRegistry implements RegistryContract
{
    public function __construct(
        private SocialiteOidcAuthenticator $socialiteOidcAuthenticator,
        private SocialiteSocialAuthenticator $socialiteSocialAuthenticator,
        private Saml2Authenticator $saml2Authenticator,
    ) {}

    public function for(ProviderType $providerType): SsoAuthenticator
    {
        return match ($providerType) {
            ProviderType::Oidc => $this->socialiteOidcAuthenticator,
            ProviderType::Saml => $this->saml2Authenticator,
            ProviderType::Google, ProviderType::Microsoft, ProviderType::Github => $this->socialiteSocialAuthenticator,
        };
    }
}
