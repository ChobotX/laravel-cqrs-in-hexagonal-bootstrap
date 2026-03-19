<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\ListOrganizationMembers;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\OrganizationMember;
use App\Domain\Organization\OrganizationMemberRepository;

/** @implements QueryHandler<ListOrganizationMembersQuery, list<OrganizationMember>> */
final readonly class ListOrganizationMembersHandler implements QueryHandler
{
    public function __construct(
        private OrganizationMemberRepository $organizationMemberRepository,
    ) {}

    /** @return list<OrganizationMember> */
    public function handle(Query $query): array
    {
        return $this->organizationMemberRepository->listMembers($query->organizationId);
    }
}
