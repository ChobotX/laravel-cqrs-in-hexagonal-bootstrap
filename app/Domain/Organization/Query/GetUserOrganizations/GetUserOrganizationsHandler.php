<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetUserOrganizations;

use App\Contract\Organization\OrganizationMembershipChecker;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationRepository;

/** @implements QueryHandler<GetUserOrganizationsQuery, list<Organization>> */
final readonly class GetUserOrganizationsHandler implements QueryHandler
{
    public function __construct(
        private OrganizationMembershipChecker $organizationMembershipChecker,
        private OrganizationRepository $organizationRepository,
    ) {}

    /** @return list<Organization> */
    public function handle(Query $query): array
    {
        $orgIds = $this->organizationMembershipChecker->memberOrganizationIds($query->userId);

        if ($orgIds === []) {
            return [];
        }

        return $this->organizationRepository->findByIds($orgIds);
    }
}
