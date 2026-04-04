<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Contract\Query\GetRolesForUsers\GetRolesForUsersQuery;
use App\Domain\Authorization\Contract\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Label\Contract\Query\GetLabelsForEntities\GetLabelsForEntitiesQuery;
use App\Domain\Team\Contract\Query\GetTeamsForUsers\GetTeamsForUsersQuery;
use App\Domain\User\Contract\Query\ListUsers\ListUsersQuery;
use App\Domain\User\Contract\User;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

#[RequiresPermission('users.list.read')]
final readonly class ListUsersController
{
    private const array SORTABLE_COLUMNS = ['name', 'email'];

    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View|RedirectResponse
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $defaultSorting = new Sorting('name', SortDirection::Asc);
        $requestSorting = $paginationRequest->sorting();
        $sorting = $requestSorting instanceof Sorting && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)
            ? $requestSorting
            : $defaultSorting;

        $paginatedResult = $this->queryBus->dispatch(
            new ListUsersQuery($paginationRequest->pagination())->withSorting([$sorting]),
        );

        if ($paginatedResult->isPageOutOfBounds()) {
            return redirect(url()->current().'?'.http_build_query([...$paginationRequest->query(), 'page' => 1]));
        }

        $userIds = array_map(fn (User $user): string => $user->id->value, $paginatedResult->items);

        $canReadRoles = $this->authorizationChecker->can($currentUserId, 'users.roles.read');
        $userRoles = $canReadRoles ? $this->queryBus->dispatch(new GetRolesForUsersQuery($userIds)) : [];

        $isSuperAdmin = false;

        if ($canReadRoles) {
            $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($currentUserId));
            $isSuperAdmin = array_any($currentUserRoles, fn (Role $role): bool => $role->isSystem);
        }

        $canReadTeams = $this->authorizationChecker->can($currentUserId, 'teams.members.read');
        $userTeams = $canReadTeams ? $this->queryBus->dispatch(new GetTeamsForUsersQuery($userIds)) : [];

        $canReadLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.read');
        $userLabels = $canReadLabels ? $this->queryBus->dispatch(new GetLabelsForEntitiesQuery($userIds)) : [];

        return view('users.index', [
            'result' => $paginatedResult,
            'sorting' => $sorting,
            'userRoles' => $userRoles,
            'canReadRoles' => $canReadRoles,
            'canReadTeams' => $canReadTeams,
            'userTeams' => $userTeams,
            'canReadLabels' => $canReadLabels,
            'userLabels' => $userLabels,
            'isSuperAdmin' => $isSuperAdmin,
            'currentUserId' => $currentUserId,
        ]);
    }
}
