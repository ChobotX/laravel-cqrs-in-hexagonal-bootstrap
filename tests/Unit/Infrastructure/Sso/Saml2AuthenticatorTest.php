<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use App\Infrastructure\Sso\Saml2Authenticator;
use OneLogin\Saml2\Auth as OneLoginAuth;
use OneLogin\Saml2\Settings as OneLoginSettings;

/** @param array<string, scalar|array<int|string, mixed>|null> $config */
function samlConfig(array $config = []): SsoConfiguration
{
    $now = new DateTimeImmutable;

    return new SsoConfiguration(
        id: new SsoConfigurationId('11111111-1111-1111-1111-111111111111'),
        providerType: ProviderType::Saml,
        slug: 'primary',
        displayName: 'Primary',
        enabled: true,
        enforce: false,
        jitMode: JitMode::InvitedOnly,
        allowedEmailDomains: new AllowedEmailDomains([]),
        config: $config !== [] ? $config : [
            'sp' => [
                'entityId' => 'https://app.example.com/metadata',
                'assertionConsumerService' => [
                    'url' => 'https://app.example.com/auth/sso/saml/primary/acs',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ],
            ],
            'idp' => [
                'entityId' => 'https://idp.example.com',
                'singleSignOnService' => [
                    'url' => 'https://idp.example.com/sso',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'x509cert' => 'MIIBITCB',
            ],
        ],
        createdAt: $now,
        updatedAt: $now,
    );
}

final class FakeSamlAuth extends OneLoginAuth
{
    /**
     * @param  array<string, list<string>>  $attributesValue
     * @param  list<string>  $errorList
     */
    public function __construct(
        public string $loginRedirect = 'https://idp.example.com/sso?SAMLRequest=abc',
        public string $nameIdValue = '',
        public array $attributesValue = [],
        public bool $throwOnProcess = false,
        public array $errorList = [],
    ) {
        parent::__construct([
            'sp' => [
                'entityId' => 'https://app.example.com/metadata',
                'assertionConsumerService' => [
                    'url' => 'https://app.example.com/auth/sso/saml/primary/acs',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ],
            ],
            'idp' => [
                'entityId' => 'https://idp.example.com',
                'singleSignOnService' => [
                    'url' => 'https://idp.example.com/sso',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'x509cert' => 'MIIBITCB',
            ],
        ]);
    }

    /**
     * @param  array<int|string, mixed>  $parameters
     */
    #[Override]
    public function login($returnTo = null, array $parameters = [], $forceAuthn = false, $isPassive = false, $stay = false, $setNameIdPolicy = true, $nameIdValueReq = null): string
    {
        return $this->loginRedirect;
    }

    #[Override]
    public function processResponse($requestId = null): void
    {
        if ($this->throwOnProcess) {
            throw new RuntimeException('processing exploded');
        }
    }

    /** @return list<string> */
    #[Override]
    public function getErrors(): array
    {
        return $this->errorList;
    }

    #[Override]
    public function getNameId(): string
    {
        return $this->nameIdValue;
    }

    /** @return array<string, list<string>> */
    #[Override]
    public function getAttributes(): array
    {
        return $this->attributesValue;
    }
}

$samlWithFakeAuth = fn (FakeSamlAuth $fakeSamlAuth): Saml2Authenticator => new Saml2Authenticator(
    authFactory: fn (array $settings): OneLoginAuth => $fakeSamlAuth,
);

$samlWithFakeSettingsValidator = fn (bool $throws): Saml2Authenticator => new Saml2Authenticator(
    settingsFactory: fn (array $settings): OneLoginSettings => $throws
        ? throw new RuntimeException('settings invalid')
        : new OneLoginSettings($settings),
);

it('rejects initiate without a valid configuration', function (): void {
    (new Saml2Authenticator)->initiate(samlConfig(['sp' => null, 'idp' => null]));
})->throws(SsoConfigurationInvalidException::class);

it('rejects complete without a SAMLResponse', function (): void {
    (new Saml2Authenticator)->complete(samlConfig(), []);
})->throws(SsoLoginRejectedException::class, 'missing_saml_response');

it('rejects an invalid SAMLResponse payload', function (): void {
    (new Saml2Authenticator)->complete(samlConfig(), ['SAMLResponse' => 'not-base64-signed']);
})->throws(SsoLoginRejectedException::class);

it('probe returns failure for invalid settings', function (): void {
    $ssoConnectionTestResult = (new Saml2Authenticator)->probe(samlConfig(['sp' => null, 'idp' => null]));

    expect($ssoConnectionTestResult->success)->toBeFalse();
});

it('builds a redirect URL via the factory', function () use ($samlWithFakeAuth): void {
    $redirectInstruction = $samlWithFakeAuth(new FakeSamlAuth)->initiate(samlConfig());

    expect($redirectInstruction->url)->toStartWith('https://idp.example.com/sso?');
});

it('rejects when the factory returns an empty redirect', function () use ($samlWithFakeAuth): void {
    $samlWithFakeAuth(new FakeSamlAuth(loginRedirect: ''))->initiate(samlConfig());
})->throws(SsoLoginRejectedException::class, 'saml_redirect_failed');

it('rejects when processResponse throws', function () use ($samlWithFakeAuth): void {
    $samlWithFakeAuth(new FakeSamlAuth(throwOnProcess: true))->complete(samlConfig(), ['SAMLResponse' => 'x']);
})->throws(SsoLoginRejectedException::class, 'saml_processing_failed');

it('rejects when SAML returns errors', function () use ($samlWithFakeAuth): void {
    $samlWithFakeAuth(new FakeSamlAuth(errorList: ['invalid_response']))->complete(samlConfig(), ['SAMLResponse' => 'x']);
})->throws(SsoLoginRejectedException::class, 'saml_invalid_response');

it('rejects when nameId is empty', function () use ($samlWithFakeAuth): void {
    $samlWithFakeAuth(new FakeSamlAuth(nameIdValue: ''))->complete(samlConfig(), ['SAMLResponse' => 'x']);
})->throws(SsoLoginRejectedException::class, 'missing_required_claims');

it('builds an SsoIdentity from email + name attributes', function () use ($samlWithFakeAuth): void {
    $ssoIdentity = $samlWithFakeAuth(new FakeSamlAuth(
        nameIdValue: 'subject-1',
        attributesValue: [
            'email' => ['user@example.com'],
            'name' => ['User'],
        ],
    ))->complete(samlConfig(), ['SAMLResponse' => 'x']);

    expect($ssoIdentity->subject)->toBe('subject-1')
        ->and($ssoIdentity->email)->toBe('user@example.com')
        ->and($ssoIdentity->name)->toBe('User');
});

it('falls back to nameId when no email attribute is present', function () use ($samlWithFakeAuth): void {
    $ssoIdentity = $samlWithFakeAuth(new FakeSamlAuth(
        nameIdValue: 'user@example.com',
        attributesValue: [],
    ))->complete(samlConfig(), ['SAMLResponse' => 'x']);

    expect($ssoIdentity->email)->toBe('user@example.com');
});

it('probe surfaces validation failures as a UI result', function () use ($samlWithFakeSettingsValidator): void {
    $ssoConnectionTestResult = $samlWithFakeSettingsValidator(true)->probe(samlConfig());

    expect($ssoConnectionTestResult->success)->toBeFalse();
});

it('probe returns success for valid settings via the real factory', function (): void {
    $ssoConnectionTestResult = (new Saml2Authenticator)->probe(samlConfig());

    expect($ssoConnectionTestResult->success)->toBeTrue();
});
