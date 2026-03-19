<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\ListOrganizations;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationRepository;

/** @implements QueryHandler<ListOrganizationsQuery, list<Organization>> */
final readonly class ListOrganizationsHandler implements QueryHandler
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
    ) {}

    /** @return list<Organization> */
    public function handle(Query $query): array
    {
        return $this->organizationRepository->findAll();
    }
}
