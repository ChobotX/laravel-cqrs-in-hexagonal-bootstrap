<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth\Sso;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\GetEnabledSsoProvidersQuery;
use App\Domain\Sso\Contract\Query\GetSsoConfigurationByIdQuery;
use App\Domain\Sso\Contract\ValueObject\EnabledSsoProvider;
use Illuminate\Http\Response;

use function is_array;
use function response;

#[SkipPermissionCheck('Public SAML SP metadata endpoint; consumed by IdP onboarding tooling.')]
final readonly class SamlMetadataController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $slug): Response
    {
        /** @var list<EnabledSsoProvider> $providers */
        $providers = $this->queryBus->dispatch(new GetEnabledSsoProvidersQuery);

        $configurationId = null;

        foreach ($providers as $provider) {
            if ($provider->slug === $slug && $provider->providerType === ProviderType::Saml) {
                $configurationId = $provider->configurationId;

                break;
            }
        }

        if ($configurationId === null) {
            throw new SsoConfigurationNotFoundException($slug);
        }

        /** @var SsoConfiguration $ssoConfiguration */
        $ssoConfiguration = $this->queryBus->dispatch(new GetSsoConfigurationByIdQuery($configurationId));

        /** @var array{entityId?: string, assertionConsumerService?: array{url?: string}} $sp */
        $sp = is_array($ssoConfiguration->config['sp'] ?? null) ? $ssoConfiguration->config['sp'] : [];
        $entityId = $sp['entityId'] ?? '';
        $acsUrl = $sp['assertionConsumerService']['url'] ?? '';

        $xml = sprintf(
            '<?xml version="1.0"?><md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="%s"><md:SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"><md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="%s" index="0"/></md:SPSSODescriptor></md:EntityDescriptor>',
            htmlspecialchars($entityId, ENT_XML1),
            htmlspecialchars($acsUrl, ENT_XML1),
        );

        return response($xml, HttpStatus::OK, ['Content-Type' => 'application/xml']);
    }
}
