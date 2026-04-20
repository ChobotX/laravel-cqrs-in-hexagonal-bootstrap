<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;

/**
 * Hardcoded endpoint map for the supported OAuth2 social providers.
 *
 * Lives in Infrastructure because the URLs are HTTP-protocol details, not
 * domain knowledge.
 */
final readonly class SocialProviderCatalog
{
    /** @var array<string, array{authorize: string, token: string, userinfo: string, scope: string}> */
    private const array PROVIDERS = [
        'google' => [
            'authorize' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token' => 'https://oauth2.googleapis.com/token',
            'userinfo' => 'https://openidconnect.googleapis.com/v1/userinfo',
            'scope' => 'openid email profile',
        ],
        'microsoft' => [
            'authorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'token' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'userinfo' => 'https://graph.microsoft.com/oidc/userinfo',
            'scope' => 'openid email profile',
        ],
        'github' => [
            'authorize' => 'https://github.com/login/oauth/authorize',
            'token' => 'https://github.com/login/oauth/access_token',
            'userinfo' => 'https://api.github.com/user',
            'scope' => 'read:user user:email',
        ],
    ];

    /** @return array{authorize: string, token: string, userinfo: string, scope: string} */
    public function endpointsFor(string $providerKey): array
    {
        if (! isset(self::PROVIDERS[$providerKey])) {
            throw new SsoConfigurationInvalidException('Unsupported social provider: '.$providerKey);
        }

        return self::PROVIDERS[$providerKey];
    }
}
