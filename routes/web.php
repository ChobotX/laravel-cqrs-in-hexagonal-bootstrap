<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Web\AuditLog\ListAuditLogController;
use App\Presentation\Http\Controller\Web\AuditLog\ShowAuditLogTraceController;
use App\Presentation\Http\Controller\Web\Auth\AcceptInviteController;
use App\Presentation\Http\Controller\Web\Auth\ForgotPasswordController;
use App\Presentation\Http\Controller\Web\Auth\IssueTwoFactorEmailCodeController;
use App\Presentation\Http\Controller\Web\Auth\LoginController;
use App\Presentation\Http\Controller\Web\Auth\LogoutController;
use App\Presentation\Http\Controller\Web\Auth\ResetPasswordController;
use App\Presentation\Http\Controller\Web\Auth\ShowAcceptInviteController;
use App\Presentation\Http\Controller\Web\Auth\ShowForgotPasswordController;
use App\Presentation\Http\Controller\Web\Auth\ShowLoginController;
use App\Presentation\Http\Controller\Web\Auth\ShowResetPasswordController;
use App\Presentation\Http\Controller\Web\Auth\ShowTwoFactorChallengeController;
use App\Presentation\Http\Controller\Web\Auth\VerifyTwoFactorChallengeController;
use App\Presentation\Http\Controller\Web\Authorization\CreateRoleController as WebCreateRoleController;
use App\Presentation\Http\Controller\Web\Authorization\DeleteRoleController as WebDeleteRoleController;
use App\Presentation\Http\Controller\Web\Authorization\ListRolesController as WebListRolesController;
use App\Presentation\Http\Controller\Web\Authorization\ManageUserPermissionsController;
use App\Presentation\Http\Controller\Web\Authorization\ShowCreateRoleController;
use App\Presentation\Http\Controller\Web\Authorization\ShowEditRoleController;
use App\Presentation\Http\Controller\Web\Authorization\ShowRoleController;
use App\Presentation\Http\Controller\Web\Authorization\StartImpersonationController as WebStartImpersonationController;
use App\Presentation\Http\Controller\Web\Authorization\StopImpersonationController as WebStopImpersonationController;
use App\Presentation\Http\Controller\Web\Authorization\UpdateRoleController as WebUpdateRoleController;
use App\Presentation\Http\Controller\Web\Dashboard\DashboardController;
use App\Presentation\Http\Controller\Web\FeatureFlag\ListFeatureFlagsController;
use App\Presentation\Http\Controller\Web\FeatureFlag\ResetFeatureFlagController;
use App\Presentation\Http\Controller\Web\FeatureFlag\ShowEditFeatureFlagController;
use App\Presentation\Http\Controller\Web\FeatureFlag\UpdateFeatureFlagController;
use App\Presentation\Http\Controller\Web\File\ServeFileController;
use App\Presentation\Http\Controller\Web\GridPreset\DeleteGridPresetController;
use App\Presentation\Http\Controller\Web\GridPreset\SaveGridPresetController;
use App\Presentation\Http\Controller\Web\GridPreset\SetDefaultGridPresetController;
use App\Presentation\Http\Controller\Web\Locale\SwitchLocaleController;
use App\Presentation\Http\Controller\Web\Notification\ShowNotificationsController;
use App\Presentation\Http\Controller\Web\Registry\ActivateDefinitionVersionController;
use App\Presentation\Http\Controller\Web\Registry\CreateDefinitionController;
use App\Presentation\Http\Controller\Web\Registry\CreateDefinitionVersionController;
use App\Presentation\Http\Controller\Web\Registry\CreateEntryController;
use App\Presentation\Http\Controller\Web\Registry\DeleteDefinitionController;
use App\Presentation\Http\Controller\Web\Registry\DeleteEntryController;
use App\Presentation\Http\Controller\Web\Registry\ListDefinitionsController;
use App\Presentation\Http\Controller\Web\Registry\ListEntriesController;
use App\Presentation\Http\Controller\Web\Registry\ShowCreateDefinitionController;
use App\Presentation\Http\Controller\Web\Registry\ShowCreateEntryController;
use App\Presentation\Http\Controller\Web\Registry\ShowDefinitionController;
use App\Presentation\Http\Controller\Web\Registry\ShowEditDefinitionController;
use App\Presentation\Http\Controller\Web\Registry\ShowEditEntryController;
use App\Presentation\Http\Controller\Web\Registry\UpdateDefinitionController;
use App\Presentation\Http\Controller\Web\Registry\UpdateEntryController;
use App\Presentation\Http\Controller\Web\Settings\DownloadOwnTotpBackupCodesController;
use App\Presentation\Http\Controller\Web\Settings\EditEmailTemplateController;
use App\Presentation\Http\Controller\Web\Settings\ListEmailLogsController;
use App\Presentation\Http\Controller\Web\Settings\ListEmailTemplatesController;
use App\Presentation\Http\Controller\Web\Settings\PreviewEmailTemplateController;
use App\Presentation\Http\Controller\Web\Settings\ResetEmailTemplateController;
use App\Presentation\Http\Controller\Web\Settings\ShowOwnTwoFactorSettingsController;
use App\Presentation\Http\Controller\Web\Settings\ShowPasswordRotationSettingsController;
use App\Presentation\Http\Controller\Web\Settings\ShowTenantSettingsController;
use App\Presentation\Http\Controller\Web\Settings\ShowTwoFactorSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdateEmailTemplateController;
use App\Presentation\Http\Controller\Web\Settings\UpdateOwnTwoFactorSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdatePasswordRotationSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdateTenantSettingsController;
use App\Presentation\Http\Controller\Web\Settings\UpdateTwoFactorSettingsController;
use App\Presentation\Http\Controller\Web\Team\CreateTeamController;
use App\Presentation\Http\Controller\Web\Team\DeleteTeamController;
use App\Presentation\Http\Controller\Web\Team\ListTeamsController;
use App\Presentation\Http\Controller\Web\Team\ManageTeamMembersController;
use App\Presentation\Http\Controller\Web\Team\ShowCreateTeamController;
use App\Presentation\Http\Controller\Web\Team\ShowEditTeamController;
use App\Presentation\Http\Controller\Web\Team\ShowTeamController;
use App\Presentation\Http\Controller\Web\Team\UpdateTeamController;
use App\Presentation\Http\Controller\Web\User\CreateUserController;
use App\Presentation\Http\Controller\Web\User\DeleteUserController;
use App\Presentation\Http\Controller\Web\User\ListUsersController;
use App\Presentation\Http\Controller\Web\User\ResendInviteController;
use App\Presentation\Http\Controller\Web\User\ResetUserTwoFactorController;
use App\Presentation\Http\Controller\Web\User\ShowCreateUserController;
use App\Presentation\Http\Controller\Web\User\ShowEditUserController;
use App\Presentation\Http\Controller\Web\User\ShowProfileController;
use App\Presentation\Http\Controller\Web\User\UpdateProfileController;
use App\Presentation\Http\Controller\Web\User\UpdateUserController;
use Illuminate\Support\Facades\Route;

Route::post('/locale', SwitchLocaleController::class)->name('locale.update');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', ShowLoginController::class)->name('login');
    Route::post('/login', LoginController::class)->middleware('throttle:login');

    Route::get('/invite/{userId}', ShowAcceptInviteController::class)->name('invite.accept')->middleware('signed:relative');
    Route::post('/invite/{userId}', AcceptInviteController::class)->name('invite.accept.store')->middleware('signed:relative');

    Route::get('/forgot-password', ShowForgotPasswordController::class)->name('password.request');
    Route::post('/forgot-password', ForgotPasswordController::class)->name('password.email')->middleware('throttle:password-reset');
    Route::get('/reset-password/{token}', ShowResetPasswordController::class)->name('password.reset');
    Route::post('/reset-password', ResetPasswordController::class)->name('password.update');

    Route::get('/auth/sso/saml/{slug}/metadata', App\Presentation\Http\Controller\Web\Auth\Sso\SamlMetadataController::class)->name('sso.saml.metadata');
    Route::get('/auth/sso/{slug}', App\Presentation\Http\Controller\Web\Auth\Sso\InitiateSsoLoginController::class)->name('sso.initiate');
    Route::get('/auth/sso/{slug}/callback', App\Presentation\Http\Controller\Web\Auth\Sso\SsoCallbackController::class)->name('sso.callback');
    Route::post('/auth/sso/saml/{slug}/acs', App\Presentation\Http\Controller\Web\Auth\Sso\SamlAcsController::class)->name('sso.saml.acs');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', ShowProfileController::class)->name('profile');
    Route::put('/profile', UpdateProfileController::class)->name('profile.update');
    Route::get('/profile/two-factor', ShowOwnTwoFactorSettingsController::class)->name('profile.two-factor');
    Route::get('/profile/two-factor/backup-codes', DownloadOwnTotpBackupCodesController::class)->name('profile.two-factor.backup-codes.download');
    Route::put('/profile/two-factor', UpdateOwnTwoFactorSettingsController::class)->name('profile.two-factor.update');
    Route::get('/notifications', ShowNotificationsController::class)->name('notifications.index');
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/two-factor', ShowTwoFactorChallengeController::class)->name('two-factor.challenge');
    Route::post('/two-factor/verify', VerifyTwoFactorChallengeController::class)->name('two-factor.verify');
    Route::post('/two-factor/email-code', IssueTwoFactorEmailCodeController::class)->name('two-factor.email-code');

    Route::get('/files/{fileId}', ServeFileController::class)->name('files.show');

    Route::get('/users', ListUsersController::class)->name('users.index');
    Route::get('/users/create', ShowCreateUserController::class)->name('users.create');
    Route::post('/users', CreateUserController::class)->name('users.store');
    Route::get('/users/{userId}/edit', ShowEditUserController::class)->name('users.edit');
    Route::put('/users/{userId}', UpdateUserController::class)->name('users.update');
    Route::delete('/users/{userId}', DeleteUserController::class)->name('users.destroy');
    Route::post('/users/{userId}/permissions', ManageUserPermissionsController::class)->name('users.permissions.manage');
    Route::post('/users/{userId}/resend-invite', ResendInviteController::class)->name('users.resend-invite');
    Route::post('/users/{userId}/reset-two-factor', ResetUserTwoFactorController::class)->name('users.reset-two-factor');

    Route::get('/roles', WebListRolesController::class)->name('roles.index');
    Route::get('/roles/create', ShowCreateRoleController::class)->name('roles.create');
    Route::post('/roles', WebCreateRoleController::class)->name('roles.store');
    Route::get('/roles/{roleId}', ShowRoleController::class)->name('roles.show');
    Route::get('/roles/{roleId}/edit', ShowEditRoleController::class)->name('roles.edit');
    Route::put('/roles/{roleId}', WebUpdateRoleController::class)->name('roles.update');
    Route::delete('/roles/{roleId}', WebDeleteRoleController::class)->name('roles.destroy');

    Route::get('/teams', ListTeamsController::class)->name('teams.index');
    Route::get('/teams/create', ShowCreateTeamController::class)->name('teams.create');
    Route::post('/teams', CreateTeamController::class)->name('teams.store');
    Route::get('/teams/{teamId}', ShowTeamController::class)->name('teams.show');
    Route::get('/teams/{teamId}/edit', ShowEditTeamController::class)->name('teams.edit');
    Route::put('/teams/{teamId}', UpdateTeamController::class)->name('teams.update');
    Route::delete('/teams/{teamId}', DeleteTeamController::class)->name('teams.destroy');
    Route::post('/teams/{teamId}/members', ManageTeamMembersController::class)->name('teams.members');

    Route::get('/settings', ShowTenantSettingsController::class)->name('settings.index');
    Route::put('/settings', UpdateTenantSettingsController::class)->name('settings.update');
    Route::get('/settings/password-rotation', ShowPasswordRotationSettingsController::class)->name('settings.password-rotation');
    Route::put('/settings/password-rotation', UpdatePasswordRotationSettingsController::class)->name('settings.password-rotation.update');
    Route::get('/settings/two-factor', ShowTwoFactorSettingsController::class)->name('settings.two-factor');
    Route::put('/settings/two-factor', UpdateTwoFactorSettingsController::class)->name('settings.two-factor.update');

    // Email Templates
    Route::get('/settings/email-templates', ListEmailTemplatesController::class)->name('settings.email-templates.index');
    Route::get('/settings/email-templates/{type}/{locale}', EditEmailTemplateController::class)->name('settings.email-templates.edit')
        ->where(['type' => '[a-z_]+', 'locale' => '[a-z]{2}']);
    Route::put('/settings/email-templates/{type}/{locale}', UpdateEmailTemplateController::class)->name('settings.email-templates.update')
        ->where(['type' => '[a-z_]+', 'locale' => '[a-z]{2}']);
    Route::post('/settings/email-templates/{type}/{locale}/reset', ResetEmailTemplateController::class)->name('settings.email-templates.reset')
        ->where(['type' => '[a-z_]+', 'locale' => '[a-z]{2}']);
    Route::post('/settings/email-templates/preview', PreviewEmailTemplateController::class)->name('settings.email-templates.preview');

    // Email Logs
    Route::get('/settings/email-logs', ListEmailLogsController::class)->name('settings.email-logs.index');

    // SSO admin
    Route::get('/settings/sso', App\Presentation\Http\Controller\Web\Sso\ListSsoConfigurationsController::class)->name('settings.sso.index');
    Route::get('/settings/sso/create', App\Presentation\Http\Controller\Web\Sso\ShowCreateSsoConfigurationController::class)->name('settings.sso.create');
    Route::post('/settings/sso', App\Presentation\Http\Controller\Web\Sso\StoreSsoConfigurationController::class)->name('settings.sso.store');
    Route::get('/settings/sso/{id}/edit', App\Presentation\Http\Controller\Web\Sso\ShowEditSsoConfigurationController::class)->name('settings.sso.edit');
    Route::put('/settings/sso/{id}', App\Presentation\Http\Controller\Web\Sso\UpdateSsoConfigurationController::class)->name('settings.sso.update');
    Route::delete('/settings/sso/{id}', App\Presentation\Http\Controller\Web\Sso\DeleteSsoConfigurationController::class)->name('settings.sso.destroy');
    Route::post('/settings/sso/{id}/test', App\Presentation\Http\Controller\Web\Sso\TestSsoConfigurationController::class)->name('settings.sso.test');

    Route::post('/impersonate/{userId}', WebStartImpersonationController::class)->name('impersonation.start');
    Route::post('/stop-impersonation', WebStopImpersonationController::class)->name('impersonation.stop');

    Route::get('/feature-flags', ListFeatureFlagsController::class)->name('feature-flags.index');
    Route::get('/feature-flags/{key}', ShowEditFeatureFlagController::class)->name('feature-flags.edit')
        ->where('key', '[a-z][a-z0-9-]*\.[a-z][a-z0-9-]*');
    Route::put('/feature-flags/{key}', UpdateFeatureFlagController::class)->name('feature-flags.update')
        ->where('key', '[a-z][a-z0-9-]*\.[a-z][a-z0-9-]*');
    Route::delete('/feature-flags/{key}', ResetFeatureFlagController::class)->name('feature-flags.reset')
        ->where('key', '[a-z][a-z0-9-]*\.[a-z][a-z0-9-]*');

    Route::get('/registry', ListDefinitionsController::class)->name('registry.definitions.index');
    Route::get('/registry/create', ShowCreateDefinitionController::class)->name('registry.definitions.create');
    Route::post('/registry', CreateDefinitionController::class)->name('registry.definitions.store');
    Route::get('/registry/{namespace}/{slug}', ShowDefinitionController::class)->name('registry.definitions.show');
    Route::get('/registry/{namespace}/{slug}/edit', ShowEditDefinitionController::class)->name('registry.definitions.edit');
    Route::put('/registry/{namespace}/{slug}', UpdateDefinitionController::class)->name('registry.definitions.update');
    Route::delete('/registry/{namespace}/{slug}', DeleteDefinitionController::class)->name('registry.definitions.destroy');

    Route::post('/registry/{namespace}/{slug}/versions', CreateDefinitionVersionController::class)->name('registry.versions.store');
    Route::post('/registry/{namespace}/{slug}/versions/{version}/activate', ActivateDefinitionVersionController::class)->name('registry.versions.activate');

    Route::get('/registry/{namespace}/{slug}/entries', ListEntriesController::class)->name('registry.entries.index');
    Route::get('/registry/{namespace}/{slug}/entries/create', ShowCreateEntryController::class)->name('registry.entries.create');
    Route::post('/registry/{namespace}/{slug}/entries', CreateEntryController::class)->name('registry.entries.store');
    Route::get('/registry/{namespace}/{slug}/entries/{entryId}/edit', ShowEditEntryController::class)->name('registry.entries.edit');
    Route::put('/registry/{namespace}/{slug}/entries/{entryId}', UpdateEntryController::class)->name('registry.entries.update');
    Route::delete('/registry/{namespace}/{slug}/entries/{entryId}', DeleteEntryController::class)->name('registry.entries.destroy');

    Route::post('/grid-presets', SaveGridPresetController::class)->name('grid-presets.store');
    Route::delete('/grid-presets/{presetId}', DeleteGridPresetController::class)->name('grid-presets.destroy');
    Route::post('/grid-presets/{presetId}/default', SetDefaultGridPresetController::class)->name('grid-presets.set-default');

    Route::get('/audit-log', ListAuditLogController::class)->name('audit-log.index');
    Route::get('/audit-log/trace/{traceId}', ShowAuditLogTraceController::class)->name('audit-log.trace');
});
