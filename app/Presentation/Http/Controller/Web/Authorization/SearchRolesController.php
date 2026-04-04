<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Query\SearchRoles\SearchRolesQuery;
use App\Presentation\Http\Request\Web\Authorization\SearchRolesRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('users.roles.read')]
final readonly class SearchRolesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(SearchRolesRequest $searchRolesRequest): JsonResponse
    {
        /** @var list<Role> $roles */
        $roles = $this->queryBus->dispatch(new SearchRolesQuery(
            term: $searchRolesRequest->searchTerm(),
            excludeRoleIds: $searchRolesRequest->excludeRoleIds(),
        ));

        $data = array_map(fn (Role $role): array => [
            'id' => $role->id->value,
            'name' => $role->name->value,
            'description' => $role->description,
        ], $roles);

        return new JsonResponse(['data' => $data]);
    }
}
