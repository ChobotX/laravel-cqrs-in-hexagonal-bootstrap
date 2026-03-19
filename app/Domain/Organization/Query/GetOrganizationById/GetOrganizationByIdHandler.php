<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetOrganizationById;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationRepository;

/** @implements QueryHandler<GetOrganizationByIdQuery, Organization> */
final readonly class GetOrganizationByIdHandler implements QueryHandler
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
    ) {}

    public function handle(Query $query): Organization
    {
        $organization = $this->organizationRepository->findById(new OrganizationId($query->id));

        if (! $organization instanceof Organization) {
            throw new OrganizationNotFoundException($query->id);
        }

        return $organization;
    }
}
