<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Sso\Contract\Query\IsSsoEnforcedQuery;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;

/** @implements QueryHandler<IsSsoEnforcedQuery, bool> */
final readonly class IsSsoEnforcedHandler implements QueryHandler
{
    public function __construct(
        private SsoConfigurationRepository $repository,
    ) {}

    public function handle(Query $query): bool
    {
        return $this->repository->hasEnforcedConfiguration();
    }
}
