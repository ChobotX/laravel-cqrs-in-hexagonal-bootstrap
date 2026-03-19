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
use App\Domain\Authorization\Query\GetActiveImpersonation\GetActiveImpersonationHandler;
use App\Domain\Authorization\Query\GetActiveImpersonation\GetActiveImpersonationQuery;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesHandler;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesQuery;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsHandler;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
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
use App\Domain\Organization\Command\AddMember\AddMemberCommand;
use App\Domain\Organization\Command\AddMember\AddMemberHandler;
use App\Domain\Organization\Command\CreateOrganization\CreateOrganizationCommand;
use App\Domain\Organization\Command\CreateOrganization\CreateOrganizationHandler;
use App\Domain\Organization\Command\DeleteOrganization\DeleteOrganizationCommand;
use App\Domain\Organization\Command\DeleteOrganization\DeleteOrganizationHandler;
use App\Domain\Organization\Command\RemoveMember\RemoveMemberCommand;
use App\Domain\Organization\Command\RemoveMember\RemoveMemberHandler;
use App\Domain\Organization\Command\UpdateOrganization\UpdateOrganizationCommand;
use App\Domain\Organization\Command\UpdateOrganization\UpdateOrganizationHandler;
use App\Domain\Organization\Event\OrganizationCreated;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdHandler;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdQuery;
use App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsHandler;
use App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsQuery;
use App\Domain\Organization\Query\ListOrganizationMembers\ListOrganizationMembersHandler;
use App\Domain\Organization\Query\ListOrganizationMembers\ListOrganizationMembersQuery;
use App\Domain\Organization\Query\ListOrganizations\ListOrganizationsHandler;
use App\Domain\Organization\Query\ListOrganizations\ListOrganizationsQuery;
use App\Domain\User\Command\CreateUser\CreateUserCommand;
use App\Domain\User\Command\CreateUser\CreateUserHandler;
use App\Domain\User\Command\DeleteUser\DeleteUserCommand;
use App\Domain\User\Command\DeleteUser\DeleteUserHandler;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Domain\User\Command\SetPassword\SetPasswordHandler;
use App\Domain\User\Command\UpdateUser\UpdateUserCommand;
use App\Domain\User\Command\UpdateUser\UpdateUserHandler;
use App\Domain\User\Query\GetUserByEmail\GetUserByEmailHandler;
use App\Domain\User\Query\GetUserByEmail\GetUserByEmailQuery;
use App\Domain\User\Query\GetUserById\GetUserByIdHandler;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Domain\User\Query\ListUsers\ListUsersHandler;
use App\Domain\User\Query\ListUsers\ListUsersQuery;
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
use App\Infrastructure\Bus\QueuedEventBus;
use App\Infrastructure\Organization\EventHandler\SeedDefaultRolesOnOrganizationCreated;
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
                OrganizationCreated::class => [SeedDefaultRolesOnOrganizationCreated::class],
            ],
        ));

        $this->app->singleton(CommandBus::class, fn (): LaravelCommandBus => new LaravelCommandBus(
            container: $this->app,
            handlers: [
                CreateUserCommand::class => CreateUserHandler::class,
                UpdateUserCommand::class => UpdateUserHandler::class,
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
                CreateOrganizationCommand::class => CreateOrganizationHandler::class,
                UpdateOrganizationCommand::class => UpdateOrganizationHandler::class,
                DeleteOrganizationCommand::class => DeleteOrganizationHandler::class,
                AddMemberCommand::class => AddMemberHandler::class,
                RemoveMemberCommand::class => RemoveMemberHandler::class,
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
                GetUserByEmailQuery::class => GetUserByEmailHandler::class,
                ListUsersQuery::class => ListUsersHandler::class,
                ListRolesQuery::class => ListRolesHandler::class,
                GetRoleByIdQuery::class => GetRoleByIdHandler::class,
                GetUserRolesQuery::class => GetUserRolesHandler::class,
                GetUserOverridesQuery::class => GetUserOverridesHandler::class,
                GetEffectivePermissionsQuery::class => GetEffectivePermissionsHandler::class,
                GetRecordSharesQuery::class => GetRecordSharesHandler::class,
                GetAvailableModulesQuery::class => GetAvailableModulesHandler::class,
                GetActiveImpersonationQuery::class => GetActiveImpersonationHandler::class,
                ListOrganizationsQuery::class => ListOrganizationsHandler::class,
                GetOrganizationByIdQuery::class => GetOrganizationByIdHandler::class,
                GetUserOrganizationsQuery::class => GetUserOrganizationsHandler::class,
                ListOrganizationMembersQuery::class => ListOrganizationMembersHandler::class,
            ],
            middleware: [
                $this->app->make(AuthorizeAction::class),
            ],
        ));
    }
}
