<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\TestSsoConfigurationQuery;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\Service\SsoAuthenticatorRegistry;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;

/** @implements QueryHandler<TestSsoConfigurationQuery, SsoConnectionTestResult> */
final readonly class TestSsoConfigurationHandler implements QueryHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
        private SsoAuthenticatorRegistry $ssoAuthenticatorRegistry,
    ) {}

    public function handle(Query $query): SsoConnectionTestResult
    {
        $configuration = $this->ssoConfigurationRepository->findById(new SsoConfigurationId($query->id));

        if (! $configuration instanceof SsoConfiguration) {
            throw new SsoConfigurationNotFoundException($query->id);
        }

        return $this->ssoAuthenticatorRegistry->for($configuration->providerType)->probe($configuration);
    }
}
