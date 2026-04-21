<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Infrastructure\Sso\GithubEmailFetcher;
use App\Infrastructure\Sso\OidcDiscoveryClient;
use App\Infrastructure\Sso\Saml2Authenticator;
use App\Infrastructure\Sso\SocialiteOidcAuthenticator;
use App\Infrastructure\Sso\SocialiteSocialAuthenticator;
use App\Infrastructure\Sso\SocialProviderCatalog;
use App\Infrastructure\Sso\SsoAuthenticatorRegistry;
use Illuminate\Http\Client\Factory as HttpFactory;

it('routes each provider type to the correct adapter', function (): void {
    $http = new HttpFactory;
    $oidc = new SocialiteOidcAuthenticator($http, new OidcDiscoveryClient($http));
    $social = new SocialiteSocialAuthenticator($http, new SocialProviderCatalog, new GithubEmailFetcher($http));
    $saml = new Saml2Authenticator;

    $registry = new SsoAuthenticatorRegistry($oidc, $social, $saml);

    expect($registry->for(ProviderType::Oidc))->toBe($oidc)
        ->and($registry->for(ProviderType::Saml))->toBe($saml)
        ->and($registry->for(ProviderType::Google))->toBe($social)
        ->and($registry->for(ProviderType::Microsoft))->toBe($social)
        ->and($registry->for(ProviderType::Github))->toBe($social);
});
