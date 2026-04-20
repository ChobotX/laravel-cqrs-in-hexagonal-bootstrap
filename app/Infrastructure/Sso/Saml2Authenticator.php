<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\Service\SsoAuthenticator;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use Closure;
use OneLogin\Saml2\Auth as OneLoginAuth;
use OneLogin\Saml2\Settings as OneLoginSettings;
use OneLogin\Saml2\Utils as OneLoginUtils;
use Throwable;

use function is_array;
use function is_string;

/**
 * SAML 2.0 adapter built on onelogin/php-saml.
 *
 * The OneLoginAuth + OneLoginSettings primitives are constructed through
 * injected closures so unit tests can hand-roll fakes instead of supplying
 * real cryptographic material. Production wiring uses the default closures.
 *
 * Settings are assembled per request from the tenant-encrypted `config` map; nothing
 * is cached to disk. The SAML response received at the ACS endpoint is validated by
 * the underlying library against the IdP X.509 certificate carried in `config.idp.x509cert`.
 */
final readonly class Saml2Authenticator implements SsoAuthenticator
{
    /** @var Closure(array<string, scalar|array<int|string, mixed>|null>): OneLoginAuth */
    private Closure $authFactory;

    /** @var Closure(array<string, scalar|array<int|string, mixed>|null>): OneLoginSettings */
    private Closure $settingsFactory;

    /**
     * @param  null|Closure(array<string, scalar|array<int|string, mixed>|null>): OneLoginAuth  $authFactory
     * @param  null|Closure(array<string, scalar|array<int|string, mixed>|null>): OneLoginSettings  $settingsFactory
     */
    public function __construct(?Closure $authFactory = null, ?Closure $settingsFactory = null)
    {
        $this->authFactory = $authFactory ?? static fn (array $settings): OneLoginAuth => new OneLoginAuth($settings);
        $this->settingsFactory = $settingsFactory ?? static fn (array $settings): OneLoginSettings => new OneLoginSettings($settings);
    }

    public function initiate(SsoConfiguration $ssoConfiguration): RedirectInstruction
    {
        $redirectUrl = ($this->authFactory)($this->buildSettings($ssoConfiguration))->login(
            returnTo: null,
            parameters: [],
            forceAuthn: false,
            isPassive: false,
            stay: true,
            setNameIdPolicy: true,
        );

        if ($redirectUrl === '') {
            throw new SsoLoginRejectedException('saml_redirect_failed');
        }

        return new RedirectInstruction($redirectUrl);
    }

    public function complete(SsoConfiguration $ssoConfiguration, array $callbackPayload): SsoIdentity
    {
        $samlResponse = $callbackPayload['SAMLResponse'] ?? null;

        if (! is_string($samlResponse) || $samlResponse === '') {
            throw new SsoLoginRejectedException('missing_saml_response');
        }

        OneLoginUtils::setProxyVars(true);

        try {
            $auth = ($this->authFactory)($this->buildSettings($ssoConfiguration));
            $auth->processResponse();
        } catch (Throwable $throwable) {
            throw new SsoLoginRejectedException('saml_processing_failed: '.$throwable->getMessage());
        }

        $errors = $auth->getErrors();

        if ($errors !== []) {
            throw new SsoLoginRejectedException('saml_invalid_response');
        }

        $nameId = $auth->getNameId();
        /** @var array<string, list<string>> $attributes */
        $attributes = $auth->getAttributes();
        $email = $this->extractAttribute($attributes, ['email', 'mail', 'urn:oid:1.2.840.113549.1.9.1']) ?? $nameId;
        $name = $this->extractAttribute($attributes, ['name', 'displayName', 'cn']);

        if ($nameId === '' || $email === '') {
            throw new SsoLoginRejectedException('missing_required_claims');
        }

        return new SsoIdentity(subject: $nameId, email: $email, name: $name);
    }

    public function probe(SsoConfiguration $ssoConfiguration): SsoConnectionTestResult
    {
        try {
            ($this->settingsFactory)($this->buildSettings($ssoConfiguration));
        } catch (Throwable $throwable) {
            // @silent: probe endpoint converts any settings validation error into a failure result for the admin UI.
            return new SsoConnectionTestResult(false, $throwable->getMessage());
        }

        return new SsoConnectionTestResult(true, 'SAML settings are valid.');
    }

    /**
     * @param  array<string, list<string>>  $attributes
     * @param  list<string>  $keys
     */
    private function extractAttribute(array $attributes, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($attributes[$key][0]) && $attributes[$key][0] !== '') {
                return $attributes[$key][0];
            }
        }

        return null;
    }

    /** @return array<string, scalar|array<int|string, mixed>|null> */
    private function buildSettings(SsoConfiguration $ssoConfiguration): array
    {
        $sp = $ssoConfiguration->config['sp'] ?? null;
        $idp = $ssoConfiguration->config['idp'] ?? null;

        if (! is_array($sp) || ! is_array($idp)) {
            throw new SsoConfigurationInvalidException('SAML configuration must define sp and idp sections.');
        }

        $authnRequestsSigned = $ssoConfiguration->config['sign_authn_requests'] ?? false;

        return [
            'strict' => true,
            'debug' => false,
            'sp' => $sp,
            'idp' => $idp,
            'security' => [
                'authnRequestsSigned' => $authnRequestsSigned === true,
                'wantAssertionsSigned' => true,
                'wantMessagesSigned' => false,
                'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
                'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
            ],
        ];
    }
}
