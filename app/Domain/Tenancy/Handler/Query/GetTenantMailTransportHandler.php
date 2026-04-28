<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Tenancy\Contract\Query\GetTenantMailTransportQuery;
use App\Domain\Tenancy\Contract\Repository\TenantMailTransportRepository;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;

/** @implements QueryHandler<GetTenantMailTransportQuery, MailTransport> */
final readonly class GetTenantMailTransportHandler implements QueryHandler
{
    public function __construct(
        private TenantMailTransportRepository $tenantMailTransportRepository,
    ) {}

    public function handle(Query $query): MailTransport
    {
        return $this->tenantMailTransportRepository->findCustom()
            ?? $this->tenantMailTransportRepository->default();
    }
}
