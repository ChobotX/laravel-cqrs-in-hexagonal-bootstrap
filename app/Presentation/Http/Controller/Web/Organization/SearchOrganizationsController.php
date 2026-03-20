<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Organization\Organization;
use App\Domain\Organization\Query\ListOrganizations\ListOrganizationsQuery;
use App\Presentation\Http\Request\Web\Organization\SearchOrganizationsRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('organizations.management.read')]
final readonly class SearchOrganizationsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(SearchOrganizationsRequest $searchOrganizationsRequest): JsonResponse
    {
        $term = mb_strtolower($searchOrganizationsRequest->searchTerm());
        $excludeIds = $searchOrganizationsRequest->excludeOrganizationIds();

        /** @var list<Organization> $organizations */
        $organizations = $this->queryBus->dispatch(new ListOrganizationsQuery);

        $filtered = array_values(array_filter(
            $organizations,
            fn (Organization $organization): bool => ! in_array($organization->id->value, $excludeIds, true)
                && ($term === '' || str_contains(mb_strtolower($organization->name->value), $term)),
        ));

        $data = array_map(fn (Organization $organization): array => [
            'id' => $organization->id->value,
            'name' => $organization->name->value,
            'email' => $organization->description,
        ], array_slice($filtered, 0, 50));

        return new JsonResponse(['data' => $data]);
    }
}
