<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Application\Bus\Middleware\DispatchCollectedEvents;
use App\Application\Bus\Middleware\LogBusMessage;
use App\Application\Bus\Middleware\ProjectAuditLog;
use App\Application\Bus\Middleware\WrapInTransaction;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\EventBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Event\EventCollector;
use App\Contract\Tenancy\TenantContext;
use App\Domain\AuditLog\Contract\Query\GetAuditLogByTraceId\GetAuditLogByTraceIdQuery;
use App\Domain\AuditLog\Contract\Query\ListAuditLog\ListAuditLogQuery;
use App\Domain\AuditLog\Handler\Query\GetAuditLogByTraceIdHandler;
use App\Domain\AuditLog\Handler\Query\ListAuditLogHandler;
use App\Domain\Authorization\Contract\Command\AssignRoleToUserCommand;
use App\Domain\Authorization\Contract\Command\CreateRoleCommand;
use App\Domain\Authorization\Contract\Command\DeleteRoleCommand;
use App\Domain\Authorization\Contract\Command\ManageUserPermissionsCommand;
use App\Domain\Authorization\Contract\Command\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Contract\Command\RevokeRecordShareCommand;
use App\Domain\Authorization\Contract\Command\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\Authorization\Contract\Command\SetPermissionOverrideCommand;
use App\Domain\Authorization\Contract\Command\ShareRecordCommand;
use App\Domain\Authorization\Contract\Command\StartImpersonationCommand;
use App\Domain\Authorization\Contract\Command\StopImpersonationCommand;
use App\Domain\Authorization\Contract\Command\SyncUserRolesCommand;
use App\Domain\Authorization\Contract\Command\UpdateRoleCommand;
use App\Domain\Authorization\Contract\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Contract\Event\PermissionOverrideSet;
use App\Domain\Authorization\Contract\Event\RecordShared;
use App\Domain\Authorization\Contract\Event\RecordShareRevoked;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\Event\RoleDeleted;
use App\Domain\Authorization\Contract\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Contract\Event\RoleUpdated;
use App\Domain\Authorization\Contract\Query\CountRolesQuery;
use App\Domain\Authorization\Contract\Query\GetActiveImpersonationQuery;
use App\Domain\Authorization\Contract\Query\GetAssignableRolesQuery;
use App\Domain\Authorization\Contract\Query\GetAvailableModulesQuery;
use App\Domain\Authorization\Contract\Query\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Contract\Query\GetOwnEffectivePermissionsQuery;
use App\Domain\Authorization\Contract\Query\GetOwnOverridesQuery;
use App\Domain\Authorization\Contract\Query\GetRecordSharesQuery;
use App\Domain\Authorization\Contract\Query\GetRoleByIdQuery;
use App\Domain\Authorization\Contract\Query\GetRolesForUsersQuery;
use App\Domain\Authorization\Contract\Query\GetSharesForResourceQuery;
use App\Domain\Authorization\Contract\Query\GetUserOverridesQuery;
use App\Domain\Authorization\Contract\Query\GetUserRolesQuery;
use App\Domain\Authorization\Contract\Query\ListRolesQuery;
use App\Domain\Authorization\Contract\Query\SearchRolesQuery;
use App\Domain\Authorization\EventHandler\CleanupSharesOnEntityDeleted;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnOverrideRemoved;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnOverrideSet;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRecordShared;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRecordShareRevoked;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleAssigned;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleDeleted;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleRevoked;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleUpdated;
use App\Domain\Authorization\Handler\Command\AssignRoleToUserHandler;
use App\Domain\Authorization\Handler\Command\CreateRoleHandler;
use App\Domain\Authorization\Handler\Command\DeleteRoleHandler;
use App\Domain\Authorization\Handler\Command\ManageUserPermissionsHandler;
use App\Domain\Authorization\Handler\Command\RemovePermissionOverrideHandler;
use App\Domain\Authorization\Handler\Command\RevokeRecordShareHandler;
use App\Domain\Authorization\Handler\Command\RevokeRoleFromUserHandler;
use App\Domain\Authorization\Handler\Command\SeedDefaultRolesHandler;
use App\Domain\Authorization\Handler\Command\SetPermissionOverrideHandler;
use App\Domain\Authorization\Handler\Command\ShareRecordHandler;
use App\Domain\Authorization\Handler\Command\StartImpersonationHandler;
use App\Domain\Authorization\Handler\Command\StopImpersonationHandler;
use App\Domain\Authorization\Handler\Command\SyncUserRolesHandler;
use App\Domain\Authorization\Handler\Command\UpdateRoleHandler;
use App\Domain\Authorization\Handler\Query\CountRolesHandler;
use App\Domain\Authorization\Handler\Query\GetActiveImpersonationHandler;
use App\Domain\Authorization\Handler\Query\GetAssignableRolesHandler;
use App\Domain\Authorization\Handler\Query\GetAvailableModulesHandler;
use App\Domain\Authorization\Handler\Query\GetEffectivePermissionsHandler;
use App\Domain\Authorization\Handler\Query\GetOwnEffectivePermissionsHandler;
use App\Domain\Authorization\Handler\Query\GetOwnOverridesHandler;
use App\Domain\Authorization\Handler\Query\GetRecordSharesHandler;
use App\Domain\Authorization\Handler\Query\GetRoleByIdHandler;
use App\Domain\Authorization\Handler\Query\GetRolesForUsersHandler;
use App\Domain\Authorization\Handler\Query\GetSharesForResourceHandler;
use App\Domain\Authorization\Handler\Query\GetUserOverridesHandler;
use App\Domain\Authorization\Handler\Query\GetUserRolesHandler;
use App\Domain\Authorization\Handler\Query\ListRolesHandler;
use App\Domain\Authorization\Handler\Query\SearchRolesHandler;
use App\Domain\Authorization\Middleware\AuthorizeAction;
use App\Domain\Authorization\Middleware\ResolveScopeFilter;
use App\Domain\EmailTemplate\Contract\Command\ResetEmailTemplateCommand;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\EmailTemplate\Contract\Command\UpdateEmailTemplateCommand;
use App\Domain\EmailTemplate\Contract\Event\EmailTemplateReset;
use App\Domain\EmailTemplate\Contract\Event\EmailTemplateUpdated;
use App\Domain\EmailTemplate\Contract\Event\TemplatedEmailSent;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplatePreviewQuery;
use App\Domain\EmailTemplate\Contract\Query\GetEmailTemplateQuery;
use App\Domain\EmailTemplate\Contract\Query\ListEmailLogsQuery;
use App\Domain\EmailTemplate\Contract\Query\ListEmailTemplatesQuery;
use App\Domain\EmailTemplate\EventHandler\LogEmailOnSent;
use App\Domain\EmailTemplate\Handler\Command\ResetEmailTemplateHandler;
use App\Domain\EmailTemplate\Handler\Command\SendTemplatedEmailHandler;
use App\Domain\EmailTemplate\Handler\Command\UpdateEmailTemplateHandler;
use App\Domain\EmailTemplate\Handler\Query\GetEmailTemplateHandler;
use App\Domain\EmailTemplate\Handler\Query\GetEmailTemplatePreviewHandler;
use App\Domain\EmailTemplate\Handler\Query\ListEmailLogsHandler;
use App\Domain\EmailTemplate\Handler\Query\ListEmailTemplatesHandler;
use App\Domain\FeatureFlag\Contract\Command\ResetFeatureFlagCommand;
use App\Domain\FeatureFlag\Contract\Command\UpdateFeatureFlagCommand;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagReset;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagUpdated;
use App\Domain\FeatureFlag\Contract\Query\GetAllFeatureFlagValuesQuery;
use App\Domain\FeatureFlag\Contract\Query\GetFeatureFlagQuery;
use App\Domain\FeatureFlag\Contract\Query\ListFeatureFlagsQuery;
use App\Domain\FeatureFlag\Handler\Command\ResetFeatureFlagHandler;
use App\Domain\FeatureFlag\Handler\Command\UpdateFeatureFlagHandler;
use App\Domain\FeatureFlag\Handler\Query\GetAllFeatureFlagValuesHandler;
use App\Domain\FeatureFlag\Handler\Query\GetFeatureFlagHandler;
use App\Domain\FeatureFlag\Handler\Query\ListFeatureFlagsHandler;
use App\Domain\File\Contract\Command\DeleteFileCommand;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\Command\StoreFileCommand;
use App\Domain\File\Contract\Event\FileDeleted;
use App\Domain\File\Contract\Event\FileStored;
use App\Domain\File\Contract\Query\GetFileByIdQuery;
use App\Domain\File\Contract\Query\GetFileContentQuery;
use App\Domain\File\Contract\Query\GetFileVersionsQuery;
use App\Domain\File\Contract\Query\GetLatestFileVersionQuery;
use App\Domain\File\Handler\Command\DeleteFileHandler;
use App\Domain\File\Handler\Command\StoreAvatarHandler;
use App\Domain\File\Handler\Command\StoreFileHandler;
use App\Domain\File\Handler\Query\GetFileByIdHandler;
use App\Domain\File\Handler\Query\GetFileContentHandler;
use App\Domain\File\Handler\Query\GetFileVersionsHandler;
use App\Domain\File\Handler\Query\GetLatestFileVersionHandler;
use App\Domain\GridPreset\Contract\Command\DeleteGridPresetCommand;
use App\Domain\GridPreset\Contract\Command\SaveGridPresetCommand;
use App\Domain\GridPreset\Contract\Command\SetDefaultGridPresetCommand;
use App\Domain\GridPreset\Contract\Query\GetDefaultGridPresetQuery;
use App\Domain\GridPreset\Contract\Query\GetPresetShareCapabilitiesQuery;
use App\Domain\GridPreset\Contract\Query\ListGridPresetsQuery;
use App\Domain\GridPreset\Handler\Command\DeleteGridPresetHandler;
use App\Domain\GridPreset\Handler\Command\SaveGridPresetHandler;
use App\Domain\GridPreset\Handler\Command\SetDefaultGridPresetHandler;
use App\Domain\GridPreset\Handler\Query\GetDefaultGridPresetHandler;
use App\Domain\GridPreset\Handler\Query\GetPresetShareCapabilitiesHandler;
use App\Domain\GridPreset\Handler\Query\ListGridPresetsHandler;
use App\Domain\Label\Contract\Command\AssignLabelCommand;
use App\Domain\Label\Contract\Command\CreateLabelCommand;
use App\Domain\Label\Contract\Command\RemoveLabelCommand;
use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Label\Contract\Query\GetEntityLabelsQuery;
use App\Domain\Label\Contract\Query\GetLabelsForEntitiesQuery;
use App\Domain\Label\Contract\Query\SearchLabelsQuery;
use App\Domain\Label\EventHandler\CleanupLabelsOnEntityDeleted;
use App\Domain\Label\Handler\Command\AssignLabelHandler;
use App\Domain\Label\Handler\Command\CreateLabelHandler;
use App\Domain\Label\Handler\Command\RemoveLabelHandler;
use App\Domain\Label\Handler\Command\SyncEntityLabelsHandler;
use App\Domain\Label\Handler\Query\GetEntityLabelsHandler;
use App\Domain\Label\Handler\Query\GetLabelsForEntitiesHandler;
use App\Domain\Label\Handler\Query\SearchLabelsHandler;
use App\Domain\Notification\Contract\Command\DeleteNotificationCommand;
use App\Domain\Notification\Contract\Command\MarkAllNotificationsAsReadCommand;
use App\Domain\Notification\Contract\Command\MarkNotificationAsReadCommand;
use App\Domain\Notification\Contract\Command\SendNotificationCommand;
use App\Domain\Notification\Contract\Command\UpdateNotificationPreferencesCommand;
use App\Domain\Notification\Contract\Event\AllNotificationsRead;
use App\Domain\Notification\Contract\Event\NotificationCreated;
use App\Domain\Notification\Contract\Event\NotificationDeleted;
use App\Domain\Notification\Contract\Event\NotificationPreferencesUpdated;
use App\Domain\Notification\Contract\Event\NotificationRead;
use App\Domain\Notification\Contract\Query\CountUnreadNotificationsQuery;
use App\Domain\Notification\Contract\Query\GetNotificationPreferencesQuery;
use App\Domain\Notification\Contract\Query\ListOwnNotificationsQuery;
use App\Domain\Notification\EventHandler\CleanupNotificationsOnUserDeleted;
use App\Domain\Notification\EventHandler\DeliverNotificationOnCreated;
use App\Domain\Notification\EventHandler\SendWelcomeNotificationOnUserCreated;
use App\Domain\Notification\EventHandler\UpdateUnreadCountOnNotificationChange;
use App\Domain\Notification\Handler\Command\DeleteNotificationHandler;
use App\Domain\Notification\Handler\Command\MarkAllNotificationsAsReadHandler;
use App\Domain\Notification\Handler\Command\MarkNotificationAsReadHandler;
use App\Domain\Notification\Handler\Command\SendNotificationHandler;
use App\Domain\Notification\Handler\Command\UpdateNotificationPreferencesHandler;
use App\Domain\Notification\Handler\Query\CountUnreadNotificationsHandler;
use App\Domain\Notification\Handler\Query\GetNotificationPreferencesHandler;
use App\Domain\Notification\Handler\Query\ListOwnNotificationsHandler;
use App\Domain\Registry\Contract\Command\ActivateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Command\CreateDefinitionCommand;
use App\Domain\Registry\Contract\Command\CreateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Command\CreateEntryCommand;
use App\Domain\Registry\Contract\Command\DeleteDefinitionCommand;
use App\Domain\Registry\Contract\Command\DeleteEntryCommand;
use App\Domain\Registry\Contract\Command\DeprecateDefinitionVersionCommand;
use App\Domain\Registry\Contract\Command\UpdateDefinitionCommand;
use App\Domain\Registry\Contract\Command\UpdateEntryCommand;
use App\Domain\Registry\Contract\Event\DefinitionCreated;
use App\Domain\Registry\Contract\Event\DefinitionDeleted;
use App\Domain\Registry\Contract\Event\DefinitionUpdated;
use App\Domain\Registry\Contract\Event\DefinitionVersionActivated;
use App\Domain\Registry\Contract\Event\DefinitionVersionCreated;
use App\Domain\Registry\Contract\Event\DefinitionVersionDeprecated;
use App\Domain\Registry\Contract\Event\EntryCreated;
use App\Domain\Registry\Contract\Event\EntryDeleted;
use App\Domain\Registry\Contract\Event\EntryUpdated;
use App\Domain\Registry\Contract\Query\GetActiveDefinitionVersionQuery;
use App\Domain\Registry\Contract\Query\GetDefinitionByIdQuery;
use App\Domain\Registry\Contract\Query\GetDefinitionBySlugQuery;
use App\Domain\Registry\Contract\Query\GetEntryByIdQuery;
use App\Domain\Registry\Contract\Query\GetSerializedSchemaQuery;
use App\Domain\Registry\Contract\Query\ListDefinitionNamespacesQuery;
use App\Domain\Registry\Contract\Query\ListDefinitionsQuery;
use App\Domain\Registry\Contract\Query\ListDefinitionVersionsQuery;
use App\Domain\Registry\Contract\Query\ListEntriesByDefinitionSlugQuery;
use App\Domain\Registry\Contract\Query\ListEntriesQuery;
use App\Domain\Registry\Handler\Command\ActivateDefinitionVersionHandler;
use App\Domain\Registry\Handler\Command\CreateDefinitionHandler;
use App\Domain\Registry\Handler\Command\CreateDefinitionVersionHandler;
use App\Domain\Registry\Handler\Command\CreateEntryHandler;
use App\Domain\Registry\Handler\Command\DeleteDefinitionHandler;
use App\Domain\Registry\Handler\Command\DeleteEntryHandler;
use App\Domain\Registry\Handler\Command\DeprecateDefinitionVersionHandler;
use App\Domain\Registry\Handler\Command\UpdateDefinitionHandler;
use App\Domain\Registry\Handler\Command\UpdateEntryHandler;
use App\Domain\Registry\Handler\Query\GetActiveDefinitionVersionHandler;
use App\Domain\Registry\Handler\Query\GetDefinitionByIdHandler;
use App\Domain\Registry\Handler\Query\GetDefinitionBySlugHandler;
use App\Domain\Registry\Handler\Query\GetEntryByIdHandler;
use App\Domain\Registry\Handler\Query\GetSerializedSchemaHandler;
use App\Domain\Registry\Handler\Query\ListDefinitionNamespacesHandler;
use App\Domain\Registry\Handler\Query\ListDefinitionsHandler;
use App\Domain\Registry\Handler\Query\ListDefinitionVersionsHandler;
use App\Domain\Registry\Handler\Query\ListEntriesByDefinitionSlugHandler;
use App\Domain\Registry\Handler\Query\ListEntriesHandler;
use App\Domain\Sso\Contract\Command\ConfigureSsoConfigurationCommand;
use App\Domain\Sso\Contract\Command\DeleteSsoConfigurationCommand;
use App\Domain\Sso\Contract\Command\LinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Command\LoginViaSsoCommand;
use App\Domain\Sso\Contract\Command\UnlinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Command\UpdateSsoConfigurationCommand;
use App\Domain\Sso\Contract\Event\SsoConfigurationCreated;
use App\Domain\Sso\Contract\Event\SsoConfigurationDeleted;
use App\Domain\Sso\Contract\Event\SsoConfigurationUpdated;
use App\Domain\Sso\Contract\Event\SsoIdentityLinked;
use App\Domain\Sso\Contract\Event\SsoIdentityUnlinked;
use App\Domain\Sso\Contract\Event\SsoLoginFailed;
use App\Domain\Sso\Contract\Event\SsoLoginSucceeded;
use App\Domain\Sso\Contract\Query\BuildSsoRedirectInstructionQuery;
use App\Domain\Sso\Contract\Query\GetEnabledSsoProvidersQuery;
use App\Domain\Sso\Contract\Query\GetSsoConfigurationByIdQuery;
use App\Domain\Sso\Contract\Query\IsSsoEnforcedQuery;
use App\Domain\Sso\Contract\Query\ListSsoConfigurationsQuery;
use App\Domain\Sso\Contract\Query\ListUserSsoIdentitiesQuery;
use App\Domain\Sso\Contract\Query\TestSsoConfigurationQuery;
use App\Domain\Sso\Handler\Command\ConfigureSsoConfigurationHandler;
use App\Domain\Sso\Handler\Command\DeleteSsoConfigurationHandler;
use App\Domain\Sso\Handler\Command\LinkSsoIdentityHandler;
use App\Domain\Sso\Handler\Command\LoginViaSsoHandler;
use App\Domain\Sso\Handler\Command\UnlinkSsoIdentityHandler;
use App\Domain\Sso\Handler\Command\UpdateSsoConfigurationHandler;
use App\Domain\Sso\Handler\Query\BuildSsoRedirectInstructionHandler;
use App\Domain\Sso\Handler\Query\GetEnabledSsoProvidersHandler;
use App\Domain\Sso\Handler\Query\GetSsoConfigurationByIdHandler;
use App\Domain\Sso\Handler\Query\IsSsoEnforcedHandler;
use App\Domain\Sso\Handler\Query\ListSsoConfigurationsHandler;
use App\Domain\Sso\Handler\Query\ListUserSsoIdentitiesHandler;
use App\Domain\Sso\Handler\Query\TestSsoConfigurationHandler;
use App\Domain\Team\Contract\Command\AddTeamMemberCommand;
use App\Domain\Team\Contract\Command\CreateTeamCommand;
use App\Domain\Team\Contract\Command\DeleteTeamCommand;
use App\Domain\Team\Contract\Command\ManageTeamMembershipCommand;
use App\Domain\Team\Contract\Command\RemoveTeamMemberCommand;
use App\Domain\Team\Contract\Command\SyncUserTeamsCommand;
use App\Domain\Team\Contract\Command\UpdateTeamCommand;
use App\Domain\Team\Contract\Command\UpdateTeamWithLabelsCommand;
use App\Domain\Team\Contract\Event\TeamDeleted;
use App\Domain\Team\Contract\Query\CountTeamsQuery;
use App\Domain\Team\Contract\Query\GetTeamByIdQuery;
use App\Domain\Team\Contract\Query\GetTeamsForUsersQuery;
use App\Domain\Team\Contract\Query\GetTeamTreeQuery;
use App\Domain\Team\Contract\Query\GetUserTeamsQuery;
use App\Domain\Team\Contract\Query\ListTeamMembersQuery;
use App\Domain\Team\Contract\Query\ListTeamsQuery;
use App\Domain\Team\Contract\Query\SearchTeamsQuery;
use App\Domain\Team\Handler\Command\AddTeamMemberHandler;
use App\Domain\Team\Handler\Command\CreateTeamHandler;
use App\Domain\Team\Handler\Command\DeleteTeamHandler;
use App\Domain\Team\Handler\Command\ManageTeamMembershipHandler;
use App\Domain\Team\Handler\Command\RemoveTeamMemberHandler;
use App\Domain\Team\Handler\Command\SyncUserTeamsHandler;
use App\Domain\Team\Handler\Command\UpdateTeamHandler;
use App\Domain\Team\Handler\Command\UpdateTeamWithLabelsHandler;
use App\Domain\Team\Handler\Query\CountTeamsHandler;
use App\Domain\Team\Handler\Query\GetTeamByIdHandler;
use App\Domain\Team\Handler\Query\GetTeamsForUsersHandler;
use App\Domain\Team\Handler\Query\GetTeamTreeHandler;
use App\Domain\Team\Handler\Query\GetUserTeamsHandler;
use App\Domain\Team\Handler\Query\ListTeamMembersHandler;
use App\Domain\Team\Handler\Query\ListTeamsHandler;
use App\Domain\Team\Handler\Query\SearchTeamsHandler;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Contract\Command\MigrateAllTenantsCommand;
use App\Domain\Tenancy\Contract\Command\MigrateTenantCommand;
use App\Domain\Tenancy\Contract\Command\RegisterTenantWithAdminCommand;
use App\Domain\Tenancy\Contract\Command\UpdateTenantSettingsCommand;
use App\Domain\Tenancy\Contract\Event\TenantSettingsUpdated;
use App\Domain\Tenancy\Contract\Query\GetCurrentTenantNameQuery;
use App\Domain\Tenancy\Contract\Query\GetTenantSettingsQuery;
use App\Domain\Tenancy\Handler\Command\CreateTenantHandler;
use App\Domain\Tenancy\Handler\Command\InitializeTenantAdminHandler;
use App\Domain\Tenancy\Handler\Command\MigrateAllTenantsHandler;
use App\Domain\Tenancy\Handler\Command\MigrateTenantHandler;
use App\Domain\Tenancy\Handler\Command\RegisterTenantWithAdminHandler;
use App\Domain\Tenancy\Handler\Command\UpdateTenantSettingsHandler;
use App\Domain\Tenancy\Handler\Query\GetCurrentTenantNameHandler;
use App\Domain\Tenancy\Handler\Query\GetTenantSettingsHandler;
use App\Domain\User\Contract\Command\AcceptInviteCommand;
use App\Domain\User\Contract\Command\AdminResetUserTwoFactorCommand;
use App\Domain\User\Contract\Command\ConfirmTotpSetupCommand;
use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Domain\User\Contract\Command\CreateUserWithAvatarCommand;
use App\Domain\User\Contract\Command\DeleteUserCommand;
use App\Domain\User\Contract\Command\DisableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\DisableTotpTwoFactorCommand;
use App\Domain\User\Contract\Command\EnableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\IssueEmailTwoFactorChallengeCommand;
use App\Domain\User\Contract\Command\ManageOwnTwoFactorSettingsCommand;
use App\Domain\User\Contract\Command\MarkTotpBackupCodesDownloadedCommand;
use App\Domain\User\Contract\Command\MarkUserActivatedCommand;
use App\Domain\User\Contract\Command\RequestPasswordResetCommand;
use App\Domain\User\Contract\Command\ResendUserInviteCommand;
use App\Domain\User\Contract\Command\ResetPasswordCommand;
use App\Domain\User\Contract\Command\SendUserInviteCommand;
use App\Domain\User\Contract\Command\SetPasswordCommand;
use App\Domain\User\Contract\Command\StartTotpSetupCommand;
use App\Domain\User\Contract\Command\UpdatePasswordRotationSettingsCommand;
use App\Domain\User\Contract\Command\UpdateProfileCommand;
use App\Domain\User\Contract\Command\UpdateProfileWithAvatarAndPreferencesCommand;
use App\Domain\User\Contract\Command\UpdateTwoFactorSettingsCommand;
use App\Domain\User\Contract\Command\UpdateUserCommand;
use App\Domain\User\Contract\Command\UpdateUserWithAvatarAndRelationsCommand;
use App\Domain\User\Contract\Command\VerifyTwoFactorChallengeCommand;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\PasswordResetCompleted;
use App\Domain\User\Contract\Event\PasswordResetRequested;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Contract\Event\UserDeleted;
use App\Domain\User\Contract\Event\UserInviteAccepted;
use App\Domain\User\Contract\Event\UserInviteSent;
use App\Domain\User\Contract\Query\CountUsersQuery;
use App\Domain\User\Contract\Query\GetOwnProfileQuery;
use App\Domain\User\Contract\Query\GetPasswordRotationSettingsQuery;
use App\Domain\User\Contract\Query\GetPasswordRotationStatusQuery;
use App\Domain\User\Contract\Query\GetPendingTotpBackupCodesPayloadQuery;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Domain\User\Contract\Query\GetTwoFactorSettingsQuery;
use App\Domain\User\Contract\Query\GetTwoFactorStatusQuery;
use App\Domain\User\Contract\Query\GetUserByEmailQuery;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\Query\GetUsersByIdsQuery;
use App\Domain\User\Contract\Query\ListUsersQuery;
use App\Domain\User\Contract\Query\SearchUsersQuery;
use App\Domain\User\EventHandler\SendInviteOnUserCreated;
use App\Domain\User\Handler\Command\AcceptInviteHandler;
use App\Domain\User\Handler\Command\AdminResetUserTwoFactorHandler;
use App\Domain\User\Handler\Command\ConfirmTotpSetupHandler;
use App\Domain\User\Handler\Command\CreateUserHandler;
use App\Domain\User\Handler\Command\CreateUserWithAvatarHandler;
use App\Domain\User\Handler\Command\DeleteUserHandler;
use App\Domain\User\Handler\Command\DisableEmailTwoFactorHandler;
use App\Domain\User\Handler\Command\DisableTotpTwoFactorHandler;
use App\Domain\User\Handler\Command\EnableEmailTwoFactorHandler;
use App\Domain\User\Handler\Command\IssueEmailTwoFactorChallengeHandler;
use App\Domain\User\Handler\Command\ManageOwnTwoFactorSettingsHandler;
use App\Domain\User\Handler\Command\MarkTotpBackupCodesDownloadedHandler;
use App\Domain\User\Handler\Command\MarkUserActivatedHandler;
use App\Domain\User\Handler\Command\RequestPasswordResetHandler;
use App\Domain\User\Handler\Command\ResendUserInviteHandler;
use App\Domain\User\Handler\Command\ResetPasswordHandler;
use App\Domain\User\Handler\Command\SendUserInviteHandler;
use App\Domain\User\Handler\Command\SetPasswordHandler;
use App\Domain\User\Handler\Command\StartTotpSetupHandler;
use App\Domain\User\Handler\Command\UpdatePasswordRotationSettingsHandler;
use App\Domain\User\Handler\Command\UpdateProfileHandler;
use App\Domain\User\Handler\Command\UpdateProfileWithAvatarAndPreferencesHandler;
use App\Domain\User\Handler\Command\UpdateTwoFactorSettingsHandler;
use App\Domain\User\Handler\Command\UpdateUserHandler;
use App\Domain\User\Handler\Command\UpdateUserWithAvatarAndRelationsHandler;
use App\Domain\User\Handler\Command\VerifyTwoFactorChallengeHandler;
use App\Domain\User\Handler\Query\CountUsersHandler;
use App\Domain\User\Handler\Query\GetOwnProfileHandler;
use App\Domain\User\Handler\Query\GetPasswordRotationSettingsHandler;
use App\Domain\User\Handler\Query\GetPasswordRotationStatusHandler;
use App\Domain\User\Handler\Query\GetPendingTotpBackupCodesPayloadHandler;
use App\Domain\User\Handler\Query\GetTotpSetupHandler;
use App\Domain\User\Handler\Query\GetTwoFactorSettingsHandler;
use App\Domain\User\Handler\Query\GetTwoFactorStatusHandler;
use App\Domain\User\Handler\Query\GetUserByEmailHandler;
use App\Domain\User\Handler\Query\GetUserByIdHandler;
use App\Domain\User\Handler\Query\GetUsersByIdsHandler;
use App\Domain\User\Handler\Query\ListUsersHandler;
use App\Domain\User\Handler\Query\SearchUsersHandler;
use App\Infrastructure\Bus\InMemoryEventCollector;
use App\Infrastructure\Bus\LaravelCommandBus;
use App\Infrastructure\Bus\LaravelQueryBus;
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
                RoleAssignedToUser::class => [RefreshAuthorizationOnRoleAssigned::class],
                RoleRevokedFromUser::class => [RefreshAuthorizationOnRoleRevoked::class],
                PermissionOverrideSet::class => [RefreshAuthorizationOnOverrideSet::class],
                PermissionOverrideRemoved::class => [RefreshAuthorizationOnOverrideRemoved::class],
                RoleUpdated::class => [RefreshAuthorizationOnRoleUpdated::class],
                RoleDeleted::class => [RefreshAuthorizationOnRoleDeleted::class],
                RecordShared::class => [RefreshAuthorizationOnRecordShared::class],
                RecordShareRevoked::class => [RefreshAuthorizationOnRecordShareRevoked::class],
                UserCreated::class => [SendWelcomeNotificationOnUserCreated::class, SendInviteOnUserCreated::class],
                UserInviteSent::class => [],
                UserInviteAccepted::class => [],
                PasswordResetRequested::class => [],
                PasswordResetCompleted::class => [],
                UserDeleted::class => [CleanupLabelsOnEntityDeleted::class, CleanupSharesOnEntityDeleted::class, CleanupNotificationsOnUserDeleted::class],
                TeamDeleted::class => [CleanupLabelsOnEntityDeleted::class, CleanupSharesOnEntityDeleted::class],
                NotificationCreated::class => [DeliverNotificationOnCreated::class],
                NotificationRead::class => [UpdateUnreadCountOnNotificationChange::class],
                AllNotificationsRead::class => [UpdateUnreadCountOnNotificationChange::class],
                NotificationDeleted::class => [UpdateUnreadCountOnNotificationChange::class],
                NotificationPreferencesUpdated::class => [],
                PasswordChanged::class => [],
                FileStored::class => [],
                FileDeleted::class => [CleanupLabelsOnEntityDeleted::class, CleanupSharesOnEntityDeleted::class],
                DefinitionCreated::class => [],
                DefinitionUpdated::class => [],
                DefinitionDeleted::class => [],
                DefinitionVersionCreated::class => [],
                DefinitionVersionActivated::class => [],
                DefinitionVersionDeprecated::class => [],
                EntryCreated::class => [],
                EntryUpdated::class => [],
                EntryDeleted::class => [CleanupLabelsOnEntityDeleted::class, CleanupSharesOnEntityDeleted::class],
                FeatureFlagUpdated::class => [],
                FeatureFlagReset::class => [],
                TemplatedEmailSent::class => [LogEmailOnSent::class],
                EmailTemplateUpdated::class => [],
                EmailTemplateReset::class => [],
                TenantSettingsUpdated::class => [],
                SsoConfigurationCreated::class => [],
                SsoConfigurationUpdated::class => [],
                SsoConfigurationDeleted::class => [],
                SsoLoginSucceeded::class => [],
                SsoLoginFailed::class => [],
                SsoIdentityLinked::class => [],
                SsoIdentityUnlinked::class => [],
            ],
            tenantContext: $this->app->make(TenantContext::class),
        ));

        $this->app->singleton(CommandBus::class, fn (): LaravelCommandBus => new LaravelCommandBus(
            container: $this->app,
            handlers: [
                CreateUserCommand::class => CreateUserHandler::class,
                CreateUserWithAvatarCommand::class => CreateUserWithAvatarHandler::class,
                UpdateUserCommand::class => UpdateUserHandler::class,
                UpdateUserWithAvatarAndRelationsCommand::class => UpdateUserWithAvatarAndRelationsHandler::class,
                UpdateProfileCommand::class => UpdateProfileHandler::class,
                UpdateProfileWithAvatarAndPreferencesCommand::class => UpdateProfileWithAvatarAndPreferencesHandler::class,
                UpdatePasswordRotationSettingsCommand::class => UpdatePasswordRotationSettingsHandler::class,
                UpdateTwoFactorSettingsCommand::class => UpdateTwoFactorSettingsHandler::class,
                DeleteUserCommand::class => DeleteUserHandler::class,
                SetPasswordCommand::class => SetPasswordHandler::class,
                EnableEmailTwoFactorCommand::class => EnableEmailTwoFactorHandler::class,
                DisableEmailTwoFactorCommand::class => DisableEmailTwoFactorHandler::class,
                StartTotpSetupCommand::class => StartTotpSetupHandler::class,
                ConfirmTotpSetupCommand::class => ConfirmTotpSetupHandler::class,
                DisableTotpTwoFactorCommand::class => DisableTotpTwoFactorHandler::class,
                MarkTotpBackupCodesDownloadedCommand::class => MarkTotpBackupCodesDownloadedHandler::class,
                IssueEmailTwoFactorChallengeCommand::class => IssueEmailTwoFactorChallengeHandler::class,
                ManageOwnTwoFactorSettingsCommand::class => ManageOwnTwoFactorSettingsHandler::class,
                VerifyTwoFactorChallengeCommand::class => VerifyTwoFactorChallengeHandler::class,
                AdminResetUserTwoFactorCommand::class => AdminResetUserTwoFactorHandler::class,
                SendUserInviteCommand::class => SendUserInviteHandler::class,
                ResendUserInviteCommand::class => ResendUserInviteHandler::class,
                AcceptInviteCommand::class => AcceptInviteHandler::class,
                RequestPasswordResetCommand::class => RequestPasswordResetHandler::class,
                ResetPasswordCommand::class => ResetPasswordHandler::class,
                CreateRoleCommand::class => CreateRoleHandler::class,
                UpdateRoleCommand::class => UpdateRoleHandler::class,
                DeleteRoleCommand::class => DeleteRoleHandler::class,
                AssignRoleToUserCommand::class => AssignRoleToUserHandler::class,
                ManageUserPermissionsCommand::class => ManageUserPermissionsHandler::class,
                RevokeRoleFromUserCommand::class => RevokeRoleFromUserHandler::class,
                SyncUserRolesCommand::class => SyncUserRolesHandler::class,
                SetPermissionOverrideCommand::class => SetPermissionOverrideHandler::class,
                RemovePermissionOverrideCommand::class => RemovePermissionOverrideHandler::class,
                ShareRecordCommand::class => ShareRecordHandler::class,
                RevokeRecordShareCommand::class => RevokeRecordShareHandler::class,
                StartImpersonationCommand::class => StartImpersonationHandler::class,
                StopImpersonationCommand::class => StopImpersonationHandler::class,
                SeedDefaultRolesCommand::class => SeedDefaultRolesHandler::class,
                CreateTeamCommand::class => CreateTeamHandler::class,
                UpdateTeamCommand::class => UpdateTeamHandler::class,
                UpdateTeamWithLabelsCommand::class => UpdateTeamWithLabelsHandler::class,
                DeleteTeamCommand::class => DeleteTeamHandler::class,
                AddTeamMemberCommand::class => AddTeamMemberHandler::class,
                ManageTeamMembershipCommand::class => ManageTeamMembershipHandler::class,
                RemoveTeamMemberCommand::class => RemoveTeamMemberHandler::class,
                SyncUserTeamsCommand::class => SyncUserTeamsHandler::class,
                CreateLabelCommand::class => CreateLabelHandler::class,
                AssignLabelCommand::class => AssignLabelHandler::class,
                RemoveLabelCommand::class => RemoveLabelHandler::class,
                SyncEntityLabelsCommand::class => SyncEntityLabelsHandler::class,
                SendNotificationCommand::class => SendNotificationHandler::class,
                MarkNotificationAsReadCommand::class => MarkNotificationAsReadHandler::class,
                MarkAllNotificationsAsReadCommand::class => MarkAllNotificationsAsReadHandler::class,
                DeleteNotificationCommand::class => DeleteNotificationHandler::class,
                UpdateNotificationPreferencesCommand::class => UpdateNotificationPreferencesHandler::class,
                CreateTenantCommand::class => CreateTenantHandler::class,
                InitializeTenantAdminCommand::class => InitializeTenantAdminHandler::class,
                RegisterTenantWithAdminCommand::class => RegisterTenantWithAdminHandler::class,
                MigrateTenantCommand::class => MigrateTenantHandler::class,
                MigrateAllTenantsCommand::class => MigrateAllTenantsHandler::class,
                StoreFileCommand::class => StoreFileHandler::class,
                StoreAvatarCommand::class => StoreAvatarHandler::class,
                DeleteFileCommand::class => DeleteFileHandler::class,
                CreateDefinitionCommand::class => CreateDefinitionHandler::class,
                UpdateDefinitionCommand::class => UpdateDefinitionHandler::class,
                DeleteDefinitionCommand::class => DeleteDefinitionHandler::class,
                CreateDefinitionVersionCommand::class => CreateDefinitionVersionHandler::class,
                ActivateDefinitionVersionCommand::class => ActivateDefinitionVersionHandler::class,
                DeprecateDefinitionVersionCommand::class => DeprecateDefinitionVersionHandler::class,
                CreateEntryCommand::class => CreateEntryHandler::class,
                UpdateEntryCommand::class => UpdateEntryHandler::class,
                DeleteEntryCommand::class => DeleteEntryHandler::class,
                UpdateFeatureFlagCommand::class => UpdateFeatureFlagHandler::class,
                ResetFeatureFlagCommand::class => ResetFeatureFlagHandler::class,
                SaveGridPresetCommand::class => SaveGridPresetHandler::class,
                DeleteGridPresetCommand::class => DeleteGridPresetHandler::class,
                SetDefaultGridPresetCommand::class => SetDefaultGridPresetHandler::class,
                UpdateTenantSettingsCommand::class => UpdateTenantSettingsHandler::class,
                SendTemplatedEmailCommand::class => SendTemplatedEmailHandler::class,
                UpdateEmailTemplateCommand::class => UpdateEmailTemplateHandler::class,
                ResetEmailTemplateCommand::class => ResetEmailTemplateHandler::class,
                MarkUserActivatedCommand::class => MarkUserActivatedHandler::class,
                ConfigureSsoConfigurationCommand::class => ConfigureSsoConfigurationHandler::class,
                UpdateSsoConfigurationCommand::class => UpdateSsoConfigurationHandler::class,
                DeleteSsoConfigurationCommand::class => DeleteSsoConfigurationHandler::class,
                LoginViaSsoCommand::class => LoginViaSsoHandler::class,
                LinkSsoIdentityCommand::class => LinkSsoIdentityHandler::class,
                UnlinkSsoIdentityCommand::class => UnlinkSsoIdentityHandler::class,
            ],
            middleware: [
                $this->app->make(LogBusMessage::class),
                $this->app->make(AuthorizeAction::class),
                $this->app->make(DispatchCollectedEvents::class),
                $this->app->make(WrapInTransaction::class),
                $this->app->make(ProjectAuditLog::class),
            ],
        ));

        $this->app->singleton(QueryBus::class, fn (): LaravelQueryBus => new LaravelQueryBus(
            container: $this->app,
            handlers: [
                GetUserByIdQuery::class => GetUserByIdHandler::class,
                GetUsersByIdsQuery::class => GetUsersByIdsHandler::class,
                GetOwnProfileQuery::class => GetOwnProfileHandler::class,
                GetPasswordRotationStatusQuery::class => GetPasswordRotationStatusHandler::class,
                GetPasswordRotationSettingsQuery::class => GetPasswordRotationSettingsHandler::class,
                GetTwoFactorStatusQuery::class => GetTwoFactorStatusHandler::class,
                GetTwoFactorSettingsQuery::class => GetTwoFactorSettingsHandler::class,
                GetTotpSetupQuery::class => GetTotpSetupHandler::class,
                GetPendingTotpBackupCodesPayloadQuery::class => GetPendingTotpBackupCodesPayloadHandler::class,
                GetUserByEmailQuery::class => GetUserByEmailHandler::class,
                ListUsersQuery::class => ListUsersHandler::class,
                CountUsersQuery::class => CountUsersHandler::class,
                SearchUsersQuery::class => SearchUsersHandler::class,
                ListRolesQuery::class => ListRolesHandler::class,
                CountRolesQuery::class => CountRolesHandler::class,
                GetRoleByIdQuery::class => GetRoleByIdHandler::class,
                GetUserRolesQuery::class => GetUserRolesHandler::class,
                GetRolesForUsersQuery::class => GetRolesForUsersHandler::class,
                GetUserOverridesQuery::class => GetUserOverridesHandler::class,
                GetOwnOverridesQuery::class => GetOwnOverridesHandler::class,
                GetEffectivePermissionsQuery::class => GetEffectivePermissionsHandler::class,
                GetOwnEffectivePermissionsQuery::class => GetOwnEffectivePermissionsHandler::class,
                GetRecordSharesQuery::class => GetRecordSharesHandler::class,
                GetSharesForResourceQuery::class => GetSharesForResourceHandler::class,
                GetAvailableModulesQuery::class => GetAvailableModulesHandler::class,
                GetActiveImpersonationQuery::class => GetActiveImpersonationHandler::class,
                ListTeamsQuery::class => ListTeamsHandler::class,
                CountTeamsQuery::class => CountTeamsHandler::class,
                GetTeamByIdQuery::class => GetTeamByIdHandler::class,
                GetTeamTreeQuery::class => GetTeamTreeHandler::class,
                ListTeamMembersQuery::class => ListTeamMembersHandler::class,
                GetUserTeamsQuery::class => GetUserTeamsHandler::class,
                GetTeamsForUsersQuery::class => GetTeamsForUsersHandler::class,
                SearchTeamsQuery::class => SearchTeamsHandler::class,
                SearchRolesQuery::class => SearchRolesHandler::class,
                GetAssignableRolesQuery::class => GetAssignableRolesHandler::class,
                SearchLabelsQuery::class => SearchLabelsHandler::class,
                GetEntityLabelsQuery::class => GetEntityLabelsHandler::class,
                GetLabelsForEntitiesQuery::class => GetLabelsForEntitiesHandler::class,
                ListOwnNotificationsQuery::class => ListOwnNotificationsHandler::class,
                CountUnreadNotificationsQuery::class => CountUnreadNotificationsHandler::class,
                GetNotificationPreferencesQuery::class => GetNotificationPreferencesHandler::class,
                GetFileByIdQuery::class => GetFileByIdHandler::class,
                GetFileContentQuery::class => GetFileContentHandler::class,
                GetFileVersionsQuery::class => GetFileVersionsHandler::class,
                GetLatestFileVersionQuery::class => GetLatestFileVersionHandler::class,
                GetDefinitionByIdQuery::class => GetDefinitionByIdHandler::class,
                GetDefinitionBySlugQuery::class => GetDefinitionBySlugHandler::class,
                ListDefinitionsQuery::class => ListDefinitionsHandler::class,
                ListDefinitionNamespacesQuery::class => ListDefinitionNamespacesHandler::class,
                ListDefinitionVersionsQuery::class => ListDefinitionVersionsHandler::class,
                GetActiveDefinitionVersionQuery::class => GetActiveDefinitionVersionHandler::class,
                GetSerializedSchemaQuery::class => GetSerializedSchemaHandler::class,
                GetEntryByIdQuery::class => GetEntryByIdHandler::class,
                ListEntriesQuery::class => ListEntriesHandler::class,
                ListEntriesByDefinitionSlugQuery::class => ListEntriesByDefinitionSlugHandler::class,
                ListFeatureFlagsQuery::class => ListFeatureFlagsHandler::class,
                GetFeatureFlagQuery::class => GetFeatureFlagHandler::class,
                GetAllFeatureFlagValuesQuery::class => GetAllFeatureFlagValuesHandler::class,
                ListGridPresetsQuery::class => ListGridPresetsHandler::class,
                GetDefaultGridPresetQuery::class => GetDefaultGridPresetHandler::class,
                GetPresetShareCapabilitiesQuery::class => GetPresetShareCapabilitiesHandler::class,
                GetTenantSettingsQuery::class => GetTenantSettingsHandler::class,
                GetCurrentTenantNameQuery::class => GetCurrentTenantNameHandler::class,
                ListAuditLogQuery::class => ListAuditLogHandler::class,
                GetAuditLogByTraceIdQuery::class => GetAuditLogByTraceIdHandler::class,
                GetEmailTemplateQuery::class => GetEmailTemplateHandler::class,
                ListEmailTemplatesQuery::class => ListEmailTemplatesHandler::class,
                GetEmailTemplatePreviewQuery::class => GetEmailTemplatePreviewHandler::class,
                ListEmailLogsQuery::class => ListEmailLogsHandler::class,
                GetEnabledSsoProvidersQuery::class => GetEnabledSsoProvidersHandler::class,
                BuildSsoRedirectInstructionQuery::class => BuildSsoRedirectInstructionHandler::class,
                IsSsoEnforcedQuery::class => IsSsoEnforcedHandler::class,
                ListSsoConfigurationsQuery::class => ListSsoConfigurationsHandler::class,
                GetSsoConfigurationByIdQuery::class => GetSsoConfigurationByIdHandler::class,
                ListUserSsoIdentitiesQuery::class => ListUserSsoIdentitiesHandler::class,
                TestSsoConfigurationQuery::class => TestSsoConfigurationHandler::class,
            ],
            middleware: [
                $this->app->make(AuthorizeAction::class),
                $this->app->make(ResolveScopeFilter::class),
            ],
        ));
    }
}
