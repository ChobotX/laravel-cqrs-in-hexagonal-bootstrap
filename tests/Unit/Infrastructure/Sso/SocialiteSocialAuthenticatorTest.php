<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Infrastructure\Sso\GithubEmailFetcher;
use App\Infrastructure\Sso\SocialiteSocialAuthenticator;
use App\Infrastructure\Sso\SocialProviderCatalog;
use Illuminate\Http\Client\Factory as HttpFactory;

/** @param array<string, scalar|array<int|string, mixed>|null> $config */
function socialConfig(ProviderType $providerType, array $config = []): SsoConfiguration
{
    $now = new DateTimeImmutable;

    return new SsoConfiguration(
        id: new SsoConfigurationId('11111111-1111-1111-1111-111111111111'),
        providerType: $providerType,
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
        ], $config),
        createdAt: $now,
        updatedAt: $now,
    );
}

function makeSocial(HttpFactory $httpFactory): SocialiteSocialAuthenticator
{
    return new SocialiteSocialAuthenticator($httpFactory, new SocialProviderCatalog, new GithubEmailFetcher($httpFactory));
}

it('builds a google authorization URL', function (): void {
    $factory = new HttpFactory;
    $redirectInstruction = makeSocial($factory)->initiate(socialConfig(ProviderType::Google));

    expect($redirectInstruction->url)->toStartWith('https://accounts.google.com/o/oauth2/v2/auth?');
});

it('rejects a callback with no code', function (): void {
    makeSocial(new HttpFactory)->complete(socialConfig(ProviderType::Google), []);
})->throws(SsoLoginRejectedException::class, 'missing_code');

it('exchanges a code and returns an SsoIdentity for google', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'oauth2.googleapis.com/token' => HttpFactory::response(['access_token' => 'at'], 200),
        'openidconnect.googleapis.com/*' => HttpFactory::response(['sub' => 'g-1', 'email' => 'u@example.com', 'name' => 'U'], 200),
    ]);

    $ssoIdentity = makeSocial($factory)->complete(socialConfig(ProviderType::Google), ['code' => 'abc']);

    expect($ssoIdentity->subject)->toBe('g-1')->and($ssoIdentity->email)->toBe('u@example.com');
});

it('rejects when the token exchange fails', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'oauth2.googleapis.com/token' => HttpFactory::response([], 500),
    ]);

    makeSocial($factory)->complete(socialConfig(ProviderType::Google), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'token_exchange_failed');

it('rejects when access_token is missing', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'oauth2.googleapis.com/token' => HttpFactory::response(['expires_in' => 3600], 200),
    ]);

    makeSocial($factory)->complete(socialConfig(ProviderType::Google), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'missing_access_token');

it('rejects when the userinfo fetch fails', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'oauth2.googleapis.com/token' => HttpFactory::response(['access_token' => 'at'], 200),
        'openidconnect.googleapis.com/*' => HttpFactory::response([], 500),
    ]);

    makeSocial($factory)->complete(socialConfig(ProviderType::Google), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'userinfo_fetch_failed');

it('rejects when sub/email are missing from userinfo', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'oauth2.googleapis.com/token' => HttpFactory::response(['access_token' => 'at'], 200),
        'openidconnect.googleapis.com/*' => HttpFactory::response(['sub' => 'g-1'], 200),
    ]);

    makeSocial($factory)->complete(socialConfig(ProviderType::Google), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'missing_required_claims');

it('handles github identity with inline email', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'github.com/login/oauth/access_token' => HttpFactory::response(['access_token' => 'at'], 200),
        'api.github.com/user' => HttpFactory::response(['id' => 42, 'name' => 'G User', 'email' => 'gu@example.com'], 200),
    ]);

    $ssoIdentity = makeSocial($factory)->complete(socialConfig(ProviderType::Github), ['code' => 'abc']);

    expect($ssoIdentity->subject)->toBe('42')->and($ssoIdentity->email)->toBe('gu@example.com');
});

it('falls back to the GitHub emails endpoint when no inline email is present', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'github.com/login/oauth/access_token' => HttpFactory::response(['access_token' => 'at'], 200),
        'api.github.com/user/emails' => HttpFactory::response([
            ['email' => 'skip@example.com', 'primary' => false, 'verified' => true],
            ['email' => 'gu@example.com', 'primary' => true, 'verified' => true],
        ], 200),
        'api.github.com/user' => HttpFactory::response(['id' => 42], 200),
    ]);

    $ssoIdentity = makeSocial($factory)->complete(socialConfig(ProviderType::Github), ['code' => 'abc']);

    expect($ssoIdentity->email)->toBe('gu@example.com');
});

it('rejects GitHub identity when no verified primary email is available', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'github.com/login/oauth/access_token' => HttpFactory::response(['access_token' => 'at'], 200),
        'api.github.com/user/emails' => HttpFactory::response([], 200),
        'api.github.com/user' => HttpFactory::response(['id' => 42], 200),
    ]);

    makeSocial($factory)->complete(socialConfig(ProviderType::Github), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'missing_required_claims');

it('probe succeeds for a fully-populated configuration', function (): void {
    $ssoConnectionTestResult = makeSocial(new HttpFactory)->probe(socialConfig(ProviderType::Google));

    expect($ssoConnectionTestResult->success)->toBeTrue();
});

it('probe fails when client_id is missing', function (): void {
    $ssoConnectionTestResult = makeSocial(new HttpFactory)->probe(socialConfig(ProviderType::Google, ['client_id' => '']));

    expect($ssoConnectionTestResult->success)->toBeFalse();
});

it('probe fails when redirect_uri is missing', function (): void {
    $ssoConnectionTestResult = makeSocial(new HttpFactory)->probe(socialConfig(ProviderType::Google, ['redirect_uri' => '']));

    expect($ssoConnectionTestResult->success)->toBeFalse();
});

it('returns a failed GitHub fallback when the emails endpoint responds with non-2xx', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'api.github.com/user/emails' => HttpFactory::response([], 500),
    ]);

    expect(new GithubEmailFetcher($factory)->fetch('at'))->toBeNull();
});

it('returns null when the GitHub emails endpoint is unreachable', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'api.github.com/user/emails' => fn (): never => throw new Illuminate\Http\Client\ConnectionException('down'),
    ]);

    expect(new GithubEmailFetcher($factory)->fetch('at'))->toBeNull();
});

it('rejects when the token exchange is unreachable', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'oauth2.googleapis.com/token' => fn (): never => throw new Illuminate\Http\Client\ConnectionException('down'),
    ]);

    makeSocial($factory)->complete(socialConfig(ProviderType::Google), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'token_endpoint_unreachable');

it('probe returns failure for an unsupported provider type', function (): void {
    $ssoConnectionTestResult = makeSocial(new HttpFactory)->probe(socialConfig(ProviderType::Saml));

    expect($ssoConnectionTestResult->success)->toBeFalse()
        ->and($ssoConnectionTestResult->summary)->toContain('Unsupported social provider');
});

it('rejects when the userinfo endpoint is unreachable', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'oauth2.googleapis.com/token' => HttpFactory::response(['access_token' => 'at'], 200),
        'openidconnect.googleapis.com/*' => fn (): never => throw new Illuminate\Http\Client\ConnectionException('down'),
    ]);

    makeSocial($factory)->complete(socialConfig(ProviderType::Google), ['code' => 'abc']);
})->throws(SsoLoginRejectedException::class, 'userinfo_endpoint_unreachable');
