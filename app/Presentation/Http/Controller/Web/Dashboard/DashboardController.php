<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Dashboard;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Contract\Query\CountRoles\CountRolesQuery;
use App\Domain\Team\Contract\Query\CountTeams\CountTeamsQuery;
use App\Domain\User\Contract\Query\CountUsers\CountUsersQuery;
use Illuminate\View\View;

#[SkipPermissionCheck(reason: 'Dashboard is accessible to all authenticated users')]
final readonly class DashboardController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(): View
    {
        $userId = $this->authenticatedUser->id() ?? '';

        return view('dashboard.index', [
            'userCount' => $this->authorizationChecker->can($userId, 'users.list.read')
                ? $this->queryBus->dispatch(new CountUsersQuery)
                : null,
            'roleCount' => $this->authorizationChecker->can($userId, 'users.roles.read')
                ? $this->queryBus->dispatch(new CountRolesQuery)
                : null,
            'teamCount' => $this->authorizationChecker->can($userId, 'teams.management.read')
                ? $this->queryBus->dispatch(new CountTeamsQuery)
                : null,
        ]);
    }
}
