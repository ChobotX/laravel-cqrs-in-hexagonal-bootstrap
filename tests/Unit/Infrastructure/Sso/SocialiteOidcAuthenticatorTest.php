<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use App\Infrastructure\Sso\OidcDiscoveryClient;
use App\Infrastructure\Sso\SocialiteOidcAuthenticator;
use Illuminate\Http\Client\Factory as HttpFactory;

/** @param array<string, scalar|array<int|string, mixed>|null> $config */
function oidcConfig(array $config = []): SsoConfiguration
{
    $now = new DateTimeImmutable;

    return new SsoConfiguration(
        id: new SsoConfigurationId('11111111-1111-1111-1111-111111111111'),
        providerType: ProviderType::Oidc,
        slug: 'primary',
        displayName: 'Primary',
        enabled: true,
        enforce: false,
        jitMode: JitMode::InvitedOnly,
        allowedEmailDomains: new AllowedEmailDomains([]),
        config: array_merge([
            'client_id' => 'cid',
            'client_secret' => 'secret',
            'redirect_uri' => 'https://app.example.com/auth/sso/primary/callback',
            'discovery_url' => 'https://idp.example.com/.well-known/openid-configuration',
        ], $config),
        createdAt: $now,
        updatedAt: $now,
    );
}

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function discoveryDocument(array $overrides = []): array
{
    return array_merge([
        'authorization_endpoint' => 'https://idp.example.com/authorize',
        'token_endpoint' => 'https://idp.example.com/token',
        'issuer' => 'https://idp.example.com',
        'jwks_uri' => 'https://idp.example.com/jwks',
    ], $overrides);
}

/** @param array<string, scalar|null> $claims */
function oidcWithFakes(HttpFactory $httpFactory, array $claims = []): SocialiteOidcAuthenticator
{
    return new SocialiteOidcAuthenticator(
        $httpFactory,
        new OidcDiscoveryClient($httpFactory),
        Closure::fromCallable(fn (string $idToken, string $jwksUri): array => $claims),
    );
}

it('builds authorize URL with state and nonce', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/*' => HttpFactory::response(discoveryDocument(), 200)]);

    $redirectInstruction = oidcWithFakes($factory)->initiate(oidcConfig());

    expect($redirectInstruction->url)->toStartWith('https://idp.example.com/authorize?')
        ->and($redirectInstruction->stateToStore)->not->toBeNull()
        ->and($redirectInstruction->nonceToStore)->not->toBeNull();
});

it('rejects complete without a code', function (): void {
    oidcWithFakes(new HttpFactory)->complete(oidcConfig(), []);
})->throws(SsoLoginRejectedException::class, 'missing_code');

it('rejects when discovery misses jwks_uri', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/*' => HttpFactory::response(discoveryDocument(['jwks_uri' => '']), 200)]);

    oidcWithFakes($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoConfigurationInvalidException::class);

it('rejects when the token endpoint fails', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response([], 500),
    ]);

    oidcWithFakes($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'token_exchange_failed');

it('rejects when id_token is missing from the response', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['access_token' => 'at'], 200),
    ]);

    oidcWithFakes($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'missing_id_token');

it('returns an identity on happy path', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
    ]);

    $ssoIdentity = oidcWithFakes($factory, [
        'sub' => 'user-1',
        'email' => 'user@example.com',
        'name' => 'User',
        'aud' => 'cid',
        'iss' => 'https://idp.example.com',
        'exp' => time() + 3600,
    ])->complete(oidcConfig(), ['code' => 'abc']);

    expect($ssoIdentity->subject)->toBe('user-1')->and($ssoIdentity->email)->toBe('user@example.com');
});

it('rejects when aud claim does not match client_id', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
    ]);

    oidcWithFakes($factory, [
        'sub' => 'user-1', 'email' => 'u@example.com', 'aud' => 'other', 'iss' => 'https://idp.example.com',
    ])->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'aud_mismatch');

it('rejects when iss claim does not match discovery issuer', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
    ]);

    oidcWithFakes($factory, [
        'sub' => 'user-1', 'email' => 'u@example.com', 'aud' => 'cid', 'iss' => 'https://other.example.com',
    ])->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'iss_mismatch');

it('rejects when id_token is expired', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
    ]);

    oidcWithFakes($factory, [
        'sub' => 'user-1', 'email' => 'u@example.com', 'aud' => 'cid', 'iss' => 'https://idp.example.com', 'exp' => time() - 10,
    ])->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'id_token_expired');

it('rejects when nonce claim does not match expected', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
    ]);

    oidcWithFakes($factory, [
        'sub' => 'user-1', 'email' => 'u@example.com', 'aud' => 'cid', 'iss' => 'https://idp.example.com', 'nonce' => 'wrong',
    ])->complete(oidcConfig(), ['code' => 'abc'], 'expected');
})->throws(SsoLoginRejectedException::class, 'nonce_mismatch');

it('rejects when sub or email claim is missing', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
    ]);

    oidcWithFakes($factory, [
        'aud' => 'cid', 'iss' => 'https://idp.example.com',
    ])->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'missing_required_claims');

it('rejects when the token endpoint is unreachable', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => fn (): never => throw new Illuminate\Http\Client\ConnectionException('down'),
    ]);

    oidcWithFakes($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'token_endpoint_unreachable');

it('probe returns success when all endpoints are present', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/*' => HttpFactory::response(discoveryDocument(), 200)]);

    expect(oidcWithFakes($factory)->probe(oidcConfig())->success)->toBeTrue();
});

it('probe returns failure when the discovery document misses a field', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/*' => HttpFactory::response(discoveryDocument(['jwks_uri' => '']), 200)]);

    expect(oidcWithFakes($factory)->probe(oidcConfig())->success)->toBeFalse();
});

it('probe returns failure when discovery fetch throws', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/*' => HttpFactory::response([], 500)]);

    expect(oidcWithFakes($factory)->probe(oidcConfig())->success)->toBeFalse();
});

it('uses a default scope when none is configured', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/*' => HttpFactory::response(discoveryDocument(), 200)]);

    $redirectInstruction = oidcWithFakes($factory)->initiate(oidcConfig(['scopes' => '']));

    expect($redirectInstruction->url)->toContain('scope=openid+email+profile');
});

it('default verifier fails when JWKS endpoint is unreachable', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
        'idp.example.com/jwks' => fn (): never => throw new Illuminate\Http\Client\ConnectionException('down'),
    ]);

    new SocialiteOidcAuthenticator($factory, new OidcDiscoveryClient($factory))
        ->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class);

it('default verifier fails when JWKS returns non-2xx', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
        'idp.example.com/jwks' => HttpFactory::response([], 500),
    ]);

    new SocialiteOidcAuthenticator($factory, new OidcDiscoveryClient($factory))
        ->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'jwks_fetch_failed');

it('default verifier fails when JWKS document is empty', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'jwt'], 200),
        'idp.example.com/jwks' => HttpFactory::response(['keys' => []], 200),
    ]);

    new SocialiteOidcAuthenticator($factory, new OidcDiscoveryClient($factory))
        ->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class);

it('default verifier fails when JWKS document is malformed', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => 'malformed.jwt.parts'], 200),
        'idp.example.com/jwks' => HttpFactory::response([
            'keys' => [
                ['kty' => 'RSA', 'kid' => 'k1', 'alg' => 'RS256', 'use' => 'sig', 'n' => 'not-base64', 'e' => 'AQAB'],
            ],
        ], 200),
    ]);

    new SocialiteOidcAuthenticator($factory, new OidcDiscoveryClient($factory))
        ->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class);
