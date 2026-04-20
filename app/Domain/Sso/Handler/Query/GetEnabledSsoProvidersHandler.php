<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Query\GetEnabledSsoProvidersQuery;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\ValueObject\EnabledSsoProvider;

use function array_map;

/** @implements QueryHandler<GetEnabledSsoProvidersQuery, list<EnabledSsoProvider>> */
final readonly class GetEnabledSsoProvidersHandler implements QueryHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
    ) {}

    /** @return list<EnabledSsoProvider> */
    public function handle(Query $query): array
    {
        return array_map(
            fn (SsoConfiguration $ssoConfiguration): EnabledSsoProvider => new EnabledSsoProvider(
                configurationId: $ssoConfiguration->id->value,
                providerType: $ssoConfiguration->providerType,
                slug: $ssoConfiguration->slug,
                displayName: $ssoConfiguration->displayName,
            ),
            $this->ssoConfigurationRepository->allEnabled(),
        );
    }
}
