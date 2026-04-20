<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Enum;

/**
 * Identifies which authentication protocol/IdP a SsoConfiguration uses.
 */
enum ProviderType: string
{
    case Oidc = 'oidc';
    case Google = 'google';
    case Microsoft = 'microsoft';
    case Github = 'github';
    case Saml = 'saml';

    public function isOauthSocial(): bool
    {
        return in_array($this, [self::Google, self::Microsoft, self::Github], true);
    }
}
