<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\GetSsoConfigurationByIdQuery;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;

/** @implements QueryHandler<GetSsoConfigurationByIdQuery, SsoConfiguration> */
final readonly class GetSsoConfigurationByIdHandler implements QueryHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
    ) {}

    public function handle(Query $query): SsoConfiguration
    {
        $configuration = $this->ssoConfigurationRepository->findById(new SsoConfigurationId($query->id));

        if (! $configuration instanceof SsoConfiguration) {
            throw new SsoConfigurationNotFoundException($query->id);
        }

        return $configuration;
    }
}
