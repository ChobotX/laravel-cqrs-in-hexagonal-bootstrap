<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Query\ListSsoConfigurationsQuery;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;

/** @implements QueryHandler<ListSsoConfigurationsQuery, list<SsoConfiguration>> */
final readonly class ListSsoConfigurationsHandler implements QueryHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
    ) {}

    /** @return list<SsoConfiguration> */
    public function handle(Query $query): array
    {
        return $this->ssoConfigurationRepository->all();
    }
}
