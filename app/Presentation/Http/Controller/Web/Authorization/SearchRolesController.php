<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Role;
use App\Presentation\Http\Request\Web\Authorization\SearchRolesRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('users.roles.read')]
final readonly class SearchRolesController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(SearchRolesRequest $searchRolesRequest): JsonResponse
    {
        $orgId = $this->organizationContext->currentOrganizationId();
        $term = mb_strtolower($searchRolesRequest->searchTerm());
        $excludeIds = $searchRolesRequest->excludeRoleIds();

        /** @var list<Role> $roles */
        $roles = $this->queryBus->dispatch(new ListRolesQuery($orgId));

        $filtered = array_values(array_filter(
            $roles,
            fn (Role $role): bool => ! in_array($role->id->value, $excludeIds, true)
                && ($term === '' || str_contains(mb_strtolower($role->name->value), $term)),
        ));

        $data = array_map(fn (Role $role): array => [
            'id' => $role->id->value,
            'name' => $role->name->value,
            'description' => $role->description,
        ], $filtered);

        return new JsonResponse(['data' => $data]);
    }
}
