<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Query\GetAssignableRoles\GetAssignableRolesQuery;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesQuery;
use App\Domain\Authorization\Query\GetOwnEffectivePermissions\GetOwnEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetOwnOverrides\GetOwnOverridesQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Notification\ChannelPreference;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\Query\GetNotificationPreferences\GetNotificationPreferencesQuery;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\User\Query\GetOwnProfile\GetOwnProfileQuery;
use Illuminate\View\View;

#[SkipPermissionCheck(reason: 'Profile page is accessible to all authenticated users')]
final readonly class ShowProfileController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(): View
    {
        $userId = $this->authenticatedUser->id() ?? '';

        $user = $this->queryBus->dispatch(new GetOwnProfileQuery($userId));
        $effectivePermissions = $this->queryBus->dispatch(new GetOwnEffectivePermissionsQuery($userId));
        $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);
        $userOverrides = $this->queryBus->dispatch(new GetOwnOverridesQuery($userId));

        $canEditEmail = $this->authorizationChecker->can($userId, 'users.list.update');

        $canManageRoles = $this->authorizationChecker->can($userId, 'users.roles.read');
        $assignableRoles = [];
        $userRoleIds = [];

        if ($canManageRoles) {
            $assignableRoles = $this->queryBus->dispatch(new GetAssignableRolesQuery($userId));
            $userRoles = $this->queryBus->dispatch(new GetUserRolesQuery($userId));
            $userRoleIds = array_map(fn (Role $role): string => $role->id->value, $userRoles);
        }

        $canManageTeams = $this->authorizationChecker->can($userId, 'teams.members.update');
        $userTeams = $canManageTeams
            ? $this->queryBus->dispatch(new GetUserTeamsQuery($userId))
            : [];

        $canManageOverrides = $this->authorizationChecker->can($userId, 'users.roles.update');

        $notificationPreferences = $this->queryBus->dispatch(new GetNotificationPreferencesQuery($userId));
        $notificationPreferencesData = array_map(
            static fn (ChannelPreference $channelPreference): array => [
                'level' => $channelPreference->level->value,
                'in_app' => true,
                'email' => in_array(NotificationChannel::Email, $channelPreference->channels, true),
            ],
            $notificationPreferences->preferences,
        );

        return view('profile.edit', [
            'user' => $user,
            'canEditEmail' => $canEditEmail,
            'canManageRoles' => $canManageRoles,
            'assignableRoles' => $assignableRoles,
            'userRoleIds' => $userRoleIds,
            'canManageTeams' => $canManageTeams,
            'userTeams' => $userTeams,
            'effectivePermissions' => $effectivePermissions,
            'modules' => $modules,
            'userOverrides' => $userOverrides,
            'canManageOverrides' => $canManageOverrides,
            'notificationPreferences' => $notificationPreferencesData,
        ]);
    }
}
