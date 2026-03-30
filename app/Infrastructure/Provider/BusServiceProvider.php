<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Application\Bus\CommandBus;
use App\Application\Bus\EventBus;
use App\Application\Bus\QueryBus;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserHandler;
use App\Domain\Authorization\Command\CreateRole\CreateRoleCommand;
use App\Domain\Authorization\Command\CreateRole\CreateRoleHandler;
use App\Domain\Authorization\Command\DeleteRole\DeleteRoleCommand;
use App\Domain\Authorization\Command\DeleteRole\DeleteRoleHandler;
use App\Domain\Authorization\Command\RemovePermissionOverride\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Command\RemovePermissionOverride\RemovePermissionOverrideHandler;
use App\Domain\Authorization\Command\RevokeRecordShare\RevokeRecordShareCommand;
use App\Domain\Authorization\Command\RevokeRecordShare\RevokeRecordShareHandler;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserHandler;
use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesCommand;
use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesHandler;
use App\Domain\Authorization\Command\SetPermissionOverride\SetPermissionOverrideCommand;
use App\Domain\Authorization\Command\SetPermissionOverride\SetPermissionOverrideHandler;
use App\Domain\Authorization\Command\ShareRecord\ShareRecordCommand;
use App\Domain\Authorization\Command\ShareRecord\ShareRecordHandler;
use App\Domain\Authorization\Command\StartImpersonation\StartImpersonationCommand;
use App\Domain\Authorization\Command\StartImpersonation\StartImpersonationHandler;
use App\Domain\Authorization\Command\StopImpersonation\StopImpersonationCommand;
use App\Domain\Authorization\Command\StopImpersonation\StopImpersonationHandler;
use App\Domain\Authorization\Command\UpdateRole\UpdateRoleCommand;
use App\Domain\Authorization\Command\UpdateRole\UpdateRoleHandler;
use App\Domain\Authorization\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Event\PermissionOverrideSet;
use App\Domain\Authorization\Event\RoleAssignedToUser;
use App\Domain\Authorization\Event\RoleDeleted;
use App\Domain\Authorization\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Event\RoleUpdated;
use App\Domain\Authorization\Query\CountRoles\CountRolesHandler;
use App\Domain\Authorization\Query\CountRoles\CountRolesQuery;
use App\Domain\Authorization\Query\GetActiveImpersonation\GetActiveImpersonationHandler;
use App\Domain\Authorization\Query\GetActiveImpersonation\GetActiveImpersonationQuery;
use App\Domain\Authorization\Query\GetAssignableRoles\GetAssignableRolesHandler;
use App\Domain\Authorization\Query\GetAssignableRoles\GetAssignableRolesQuery;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesHandler;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesQuery;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsHandler;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetOwnEffectivePermissions\GetOwnEffectivePermissionsHandler;
use App\Domain\Authorization\Query\GetOwnEffectivePermissions\GetOwnEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetOwnOverrides\GetOwnOverridesHandler;
use App\Domain\Authorization\Query\GetOwnOverrides\GetOwnOverridesQuery;
use App\Domain\Authorization\Query\GetRecordShares\GetRecordSharesHandler;
use App\Domain\Authorization\Query\GetRecordShares\GetRecordSharesQuery;
use App\Domain\Authorization\Query\GetRoleById\GetRoleByIdHandler;
use App\Domain\Authorization\Query\GetRoleById\GetRoleByIdQuery;
use App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesHandler;
use App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesHandler;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Query\ListRoles\ListRolesHandler;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Query\SearchRoles\SearchRolesHandler;
use App\Domain\Authorization\Query\SearchRoles\SearchRolesQuery;
use App\Domain\Team\Command\AddTeamMember\AddTeamMemberCommand;
use App\Domain\Team\Command\AddTeamMember\AddTeamMemberHandler;
use App\Domain\Team\Command\CreateTeam\CreateTeamCommand;
use App\Domain\Team\Command\CreateTeam\CreateTeamHandler;
use App\Domain\Team\Command\DeleteTeam\DeleteTeamCommand;
use App\Domain\Team\Command\DeleteTeam\DeleteTeamHandler;
use App\Domain\Team\Command\RemoveTeamMember\RemoveTeamMemberCommand;
use App\Domain\Team\Command\RemoveTeamMember\RemoveTeamMemberHandler;
use App\Domain\Team\Command\UpdateTeam\UpdateTeamCommand;
use App\Domain\Team\Command\UpdateTeam\UpdateTeamHandler;
use App\Domain\Team\Query\CountTeams\CountTeamsHandler;
use App\Domain\Team\Query\CountTeams\CountTeamsQuery;
use App\Domain\Team\Query\GetTeamById\GetTeamByIdHandler;
use App\Domain\Team\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Team\Query\GetTeamTree\GetTeamTreeHandler;
use App\Domain\Team\Query\GetTeamTree\GetTeamTreeQuery;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsHandler;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\Team\Query\ListTeamMembers\ListTeamMembersHandler;
use App\Domain\Team\Query\ListTeamMembers\ListTeamMembersQuery;
use App\Domain\Team\Query\ListTeams\ListTeamsHandler;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use App\Domain\Team\Query\SearchTeams\SearchTeamsHandler;
use App\Domain\Team\Query\SearchTeams\SearchTeamsQuery;
use App\Domain\Tenancy\Command\CreateTenant\CreateTenantCommand;
use App\Domain\Tenancy\Command\CreateTenant\CreateTenantHandler;
use App\Domain\Tenancy\Command\MigrateAllTenants\MigrateAllTenantsCommand;
use App\Domain\Tenancy\Command\MigrateAllTenants\MigrateAllTenantsHandler;
use App\Domain\Tenancy\Command\MigrateTenant\MigrateTenantCommand;
use App\Domain\Tenancy\Command\MigrateTenant\MigrateTenantHandler;
use App\Domain\User\Command\CreateUser\CreateUserCommand;
use App\Domain\User\Command\CreateUser\CreateUserHandler;
use App\Domain\User\Command\DeleteUser\DeleteUserCommand;
use App\Domain\User\Command\DeleteUser\DeleteUserHandler;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Domain\User\Command\SetPassword\SetPasswordHandler;
use App\Domain\User\Command\UpdateProfile\UpdateProfileCommand;
use App\Domain\User\Command\UpdateProfile\UpdateProfileHandler;
use App\Domain\User\Command\UpdateUser\UpdateUserCommand;
use App\Domain\User\Command\UpdateUser\UpdateUserHandler;
use App\Domain\User\Query\CountUsers\CountUsersHandler;
use App\Domain\User\Query\CountUsers\CountUsersQuery;
use App\Domain\User\Query\GetOwnProfile\GetOwnProfileHandler;
use App\Domain\User\Query\GetOwnProfile\GetOwnProfileQuery;
use App\Domain\User\Query\GetUserByEmail\GetUserByEmailHandler;
use App\Domain\User\Query\GetUserByEmail\GetUserByEmailQuery;
use App\Domain\User\Query\GetUserById\GetUserByIdHandler;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Domain\User\Query\ListUsers\ListUsersHandler;
use App\Domain\User\Query\ListUsers\ListUsersQuery;
use App\Domain\User\Query\SearchUsers\SearchUsersHandler;
use App\Domain\User\Query\SearchUsers\SearchUsersQuery;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnOverrideRemoved;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnOverrideSet;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleAssigned;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleDeleted;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleRevoked;
use App\Infrastructure\Authorization\EventHandler\InvalidateCacheOnRoleUpdated;
use App\Infrastructure\Bus\InMemoryEventCollector;
use App\Infrastructure\Bus\LaravelCommandBus;
use App\Infrastructure\Bus\LaravelQueryBus;
use App\Infrastructure\Bus\Middleware\AuthorizeAction;
use App\Infrastructure\Bus\Middleware\DispatchCollectedEvents;
use App\Infrastructure\Bus\Middleware\ResolveScopeFilter;
use App\Infrastructure\Bus\QueuedEventBus;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Override;

final class BusServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->scoped(EventCollector::class, InMemoryEventCollector::class);

        $this->app->singleton(EventBus::class, fn (): QueuedEventBus => new QueuedEventBus(
            dispatcher: $this->app->make(Dispatcher::class),
            handlers: [
                RoleAssignedToUser::class => [InvalidateCacheOnRoleAssigned::class],
                RoleRevokedFromUser::class => [InvalidateCacheOnRoleRevoked::class],
                PermissionOverrideSet::class => [InvalidateCacheOnOverrideSet::class],
                PermissionOverrideRemoved::class => [InvalidateCacheOnOverrideRemoved::class],
                RoleUpdated::class => [InvalidateCacheOnRoleUpdated::class],
                RoleDeleted::class => [InvalidateCacheOnRoleDeleted::class],
            ],
        ));

        $this->app->singleton(CommandBus::class, fn (): LaravelCommandBus => new LaravelCommandBus(
            container: $this->app,
            handlers: [
                CreateUserCommand::class => CreateUserHandler::class,
                UpdateUserCommand::class => UpdateUserHandler::class,
                UpdateProfileCommand::class => UpdateProfileHandler::class,
                DeleteUserCommand::class => DeleteUserHandler::class,
                SetPasswordCommand::class => SetPasswordHandler::class,
                CreateRoleCommand::class => CreateRoleHandler::class,
                UpdateRoleCommand::class => UpdateRoleHandler::class,
                DeleteRoleCommand::class => DeleteRoleHandler::class,
                AssignRoleToUserCommand::class => AssignRoleToUserHandler::class,
                RevokeRoleFromUserCommand::class => RevokeRoleFromUserHandler::class,
                SetPermissionOverrideCommand::class => SetPermissionOverrideHandler::class,
                RemovePermissionOverrideCommand::class => RemovePermissionOverrideHandler::class,
                ShareRecordCommand::class => ShareRecordHandler::class,
                RevokeRecordShareCommand::class => RevokeRecordShareHandler::class,
                StartImpersonationCommand::class => StartImpersonationHandler::class,
                StopImpersonationCommand::class => StopImpersonationHandler::class,
                SeedDefaultRolesCommand::class => SeedDefaultRolesHandler::class,
                CreateTeamCommand::class => CreateTeamHandler::class,
                UpdateTeamCommand::class => UpdateTeamHandler::class,
                DeleteTeamCommand::class => DeleteTeamHandler::class,
                AddTeamMemberCommand::class => AddTeamMemberHandler::class,
                RemoveTeamMemberCommand::class => RemoveTeamMemberHandler::class,
                CreateTenantCommand::class => CreateTenantHandler::class,
                MigrateTenantCommand::class => MigrateTenantHandler::class,
                MigrateAllTenantsCommand::class => MigrateAllTenantsHandler::class,
            ],
            middleware: [
                $this->app->make(AuthorizeAction::class),
                $this->app->make(DispatchCollectedEvents::class),
            ],
        ));

        $this->app->singleton(QueryBus::class, fn (): LaravelQueryBus => new LaravelQueryBus(
            container: $this->app,
            handlers: [
                GetUserByIdQuery::class => GetUserByIdHandler::class,
                GetOwnProfileQuery::class => GetOwnProfileHandler::class,
                GetUserByEmailQuery::class => GetUserByEmailHandler::class,
                ListUsersQuery::class => ListUsersHandler::class,
                CountUsersQuery::class => CountUsersHandler::class,
                SearchUsersQuery::class => SearchUsersHandler::class,
                ListRolesQuery::class => ListRolesHandler::class,
                CountRolesQuery::class => CountRolesHandler::class,
                GetRoleByIdQuery::class => GetRoleByIdHandler::class,
                GetUserRolesQuery::class => GetUserRolesHandler::class,
                GetUserOverridesQuery::class => GetUserOverridesHandler::class,
                GetOwnOverridesQuery::class => GetOwnOverridesHandler::class,
                GetEffectivePermissionsQuery::class => GetEffectivePermissionsHandler::class,
                GetOwnEffectivePermissionsQuery::class => GetOwnEffectivePermissionsHandler::class,
                GetRecordSharesQuery::class => GetRecordSharesHandler::class,
                GetAvailableModulesQuery::class => GetAvailableModulesHandler::class,
                GetActiveImpersonationQuery::class => GetActiveImpersonationHandler::class,
                ListTeamsQuery::class => ListTeamsHandler::class,
                CountTeamsQuery::class => CountTeamsHandler::class,
                GetTeamByIdQuery::class => GetTeamByIdHandler::class,
                GetTeamTreeQuery::class => GetTeamTreeHandler::class,
                ListTeamMembersQuery::class => ListTeamMembersHandler::class,
                GetUserTeamsQuery::class => GetUserTeamsHandler::class,
                SearchTeamsQuery::class => SearchTeamsHandler::class,
                SearchRolesQuery::class => SearchRolesHandler::class,
                GetAssignableRolesQuery::class => GetAssignableRolesHandler::class,
            ],
            middleware: [
                $this->app->make(AuthorizeAction::class),
                $this->app->make(ResolveScopeFilter::class),
            ],
        ));
    }
}
