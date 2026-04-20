<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Infrastructure\Sso\JwtPayloadDecoder;
use App\Infrastructure\Sso\OidcDiscoveryClient;
use App\Infrastructure\Sso\SocialiteOidcAuthenticator;
use Illuminate\Http\Client\Factory as HttpFactory;

function oidcFactory(): HttpFactory
{
    return new HttpFactory;
}

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

/** @param array<string, scalar> $claims */
function jwt(array $claims): string
{
    $segment = fn (array $data): string => rtrim(strtr(base64_encode((string) json_encode($data)), '+/', '-_'), '=');

    return $segment(['alg' => 'RS256', 'typ' => 'JWT']).'.'.$segment($claims).'.signature';
}

/** @return array<string, string> */
function discoveryDocument(): array
{
    return [
        'authorization_endpoint' => 'https://idp.example.com/authorize',
        'token_endpoint' => 'https://idp.example.com/token',
        'issuer' => 'https://idp.example.com',
    ];
}

function makeOidc(HttpFactory $httpFactory): SocialiteOidcAuthenticator
{
    return new SocialiteOidcAuthenticator($httpFactory, new OidcDiscoveryClient($httpFactory), new JwtPayloadDecoder);
}

it('builds an authorization redirect URL', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
    ]);

    $redirectInstruction = makeOidc($factory)->initiate(oidcConfig());

    expect($redirectInstruction->url)->toStartWith('https://idp.example.com/authorize?')
        ->and($redirectInstruction->usesPostBinding)->toBeFalse();
});

it('returns a SsoIdentity for a valid callback', function (): void {
    $factory = oidcFactory();
    $idToken = jwt([
        'sub' => 'subject-1',
        'email' => 'user@example.com',
        'name' => 'User',
        'aud' => 'cid',
        'iss' => 'https://idp.example.com',
        'exp' => 2000000000,
    ]);

    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['id_token' => $idToken, 'access_token' => 'at'], 200),
    ]);

    $ssoIdentity = makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);

    expect($ssoIdentity->subject)->toBe('subject-1')
        ->and($ssoIdentity->email)->toBe('user@example.com')
        ->and($ssoIdentity->name)->toBe('User');
});

it('rejects a callback without a code', function (): void {
    makeOidc(oidcFactory())->complete(oidcConfig(), []);
})->throws(SsoLoginRejectedException::class, 'missing_code');

it('rejects a token response without an id_token', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response(['access_token' => 'at'], 200),
    ]);

    makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'missing_id_token');

it('rejects a failed token exchange', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response([], 500),
    ]);

    makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'token_exchange_failed');

it('rejects a claim set missing sub/email', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response([
            'id_token' => jwt(['sub' => 'x', 'aud' => 'cid', 'iss' => 'https://idp.example.com']),
        ], 200),
    ]);

    makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'missing_required_claims');

it('rejects an audience mismatch', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response([
            'id_token' => jwt(['sub' => 'x', 'email' => 'u@x.com', 'aud' => 'wrong']),
        ], 200),
    ]);

    makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'aud_mismatch');

it('rejects an issuer mismatch', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response([
            'id_token' => jwt(['sub' => 'x', 'email' => 'u@x.com', 'aud' => 'cid', 'iss' => 'https://other.example.com']),
        ], 200),
    ]);

    makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'iss_mismatch');

it('rejects an expired token', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response([
            'id_token' => jwt(['sub' => 'x', 'email' => 'u@x.com', 'aud' => 'cid', 'iss' => 'https://idp.example.com', 'exp' => 1]),
        ], 200),
    ]);

    makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'id_token_expired');

it('probe returns success when discovery document is complete', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
    ]);

    expect(makeOidc($factory)->probe(oidcConfig())->success)->toBeTrue();
});

it('probe returns failure when discovery document is missing endpoints', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response([], 200),
    ]);

    $ssoConnectionTestResult = makeOidc($factory)->probe(oidcConfig());

    expect($ssoConnectionTestResult->success)->toBeFalse()
        ->and($ssoConnectionTestResult->warnings)->not->toBeEmpty();
});

it('probe returns failure when discovery throws', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response([], 500),
    ]);

    $ssoConnectionTestResult = makeOidc($factory)->probe(oidcConfig());

    expect($ssoConnectionTestResult->success)->toBeFalse();
});

it('accepts tokens without an exp claim', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => HttpFactory::response([
            'id_token' => jwt(['sub' => 'x', 'email' => 'u@x.com', 'aud' => 'cid', 'iss' => 'https://idp.example.com']),
        ], 200),
    ]);

    $ssoIdentity = makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);

    expect($ssoIdentity->email)->toBe('u@x.com');
});

it('rejects when the token exchange throws a ConnectionException', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response(discoveryDocument(), 200),
        'idp.example.com/token' => fn (): never => throw new Illuminate\Http\Client\ConnectionException('down'),
    ]);

    makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'token_endpoint_unreachable');

it('skips issuer check when discovery document omits the issuer', function (): void {
    $factory = oidcFactory();
    $factory->fake([
        'idp.example.com/.well-known/openid-configuration' => HttpFactory::response([
            'authorization_endpoint' => 'https://idp.example.com/authorize',
            'token_endpoint' => 'https://idp.example.com/token',
        ], 200),
        'idp.example.com/token' => HttpFactory::response([
            'id_token' => jwt(['sub' => 'x', 'email' => 'u@x.com', 'aud' => 'cid', 'iss' => 'whatever']),
        ], 200),
    ]);

    expect(makeOidc($factory)->complete(oidcConfig(), ['code' => 'abc'])->email)->toBe('u@x.com');
});
