<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Tenancy\Contract\Query\GetCurrentTenantNameQuery;
use App\Domain\Tenancy\Contract\Service\TenantContext;

/**
 * @implements QueryHandler<GetCurrentTenantNameQuery, ?string>
 */
final readonly class GetCurrentTenantNameHandler implements QueryHandler
{
    public function __construct(
        private TenantContext $tenantContext,
    ) {}

    public function handle(Query $query): ?string
    {
        if (! $this->tenantContext->isResolved()) {
            return null;
        }

        return $this->tenantContext->currentTenantName();
    }
}
