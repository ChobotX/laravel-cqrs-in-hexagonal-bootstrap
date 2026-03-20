<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Organization\OrganizationContext;
use App\Contract\Organization\OrganizationMembershipChecker;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\User\Query\SearchUsers\SearchUsersQuery;
use App\Presentation\Http\Request\Web\User\SearchUsersRequest;
use App\Presentation\Http\Resource\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[RequiresPermission('users.list.read')]
final readonly class SearchUsersController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private OrganizationContext $organizationContext,
        private OrganizationMembershipChecker $organizationMembershipChecker,
    ) {}

    public function __invoke(SearchUsersRequest $searchUsersRequest): AnonymousResourceCollection
    {
        $userId = $this->authenticatedUser->id() ?? '';
        $orgId = $this->organizationContext->currentOrganizationId();

        /** @var list<Role> $roles */
        $roles = $this->queryBus->dispatch(new GetUserRolesQuery($userId, $orgId));
        $isSuperAdmin = array_any($roles, fn (Role $role): bool => $role->isSystem);

        $restrictToOrganizationIds = $isSuperAdmin
            ? []
            : $this->organizationMembershipChecker->memberOrganizationIds($userId);

        $term = $searchUsersRequest->searchTerm();

        $users = $this->queryBus->dispatch(new SearchUsersQuery(
            term: $term,
            restrictToOrganizationIds: $restrictToOrganizationIds,
            excludeUserIds: $searchUsersRequest->excludeUserIds(),
            limit: $term === '' ? 50 : 10,
        ));

        return UserResource::collection($users);
    }
}
