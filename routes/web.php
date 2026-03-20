<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Web\Auth\LoginController;
use App\Presentation\Http\Controller\Web\Auth\LogoutController;
use App\Presentation\Http\Controller\Web\Auth\ShowLoginController;
use App\Presentation\Http\Controller\Web\Authorization\CreateRoleController as WebCreateRoleController;
use App\Presentation\Http\Controller\Web\Authorization\DeleteRoleController as WebDeleteRoleController;
use App\Presentation\Http\Controller\Web\Authorization\ListRolesController as WebListRolesController;
use App\Presentation\Http\Controller\Web\Authorization\ShowCreateRoleController;
use App\Presentation\Http\Controller\Web\Authorization\ShowEditRoleController;
use App\Presentation\Http\Controller\Web\Authorization\ShowRoleController;
use App\Presentation\Http\Controller\Web\Authorization\StartImpersonationController as WebStartImpersonationController;
use App\Presentation\Http\Controller\Web\Authorization\StopImpersonationController as WebStopImpersonationController;
use App\Presentation\Http\Controller\Web\Authorization\UpdateRoleController as WebUpdateRoleController;
use App\Presentation\Http\Controller\Web\Authorization\UserPermissionsController;
use App\Presentation\Http\Controller\Web\DashboardController;
use App\Presentation\Http\Controller\Web\Locale\SwitchLocaleController;
use App\Presentation\Http\Controller\Web\Organization\CreateOrganizationController;
use App\Presentation\Http\Controller\Web\Organization\DeleteOrganizationController;
use App\Presentation\Http\Controller\Web\Organization\ListOrganizationsController;
use App\Presentation\Http\Controller\Web\Organization\ManageOrganizationMembersController;
use App\Presentation\Http\Controller\Web\Organization\ShowCreateOrganizationController;
use App\Presentation\Http\Controller\Web\Organization\ShowEditOrganizationController;
use App\Presentation\Http\Controller\Web\Organization\ShowOrganizationController;
use App\Presentation\Http\Controller\Web\Organization\SwitchOrganizationController;
use App\Presentation\Http\Controller\Web\Organization\UpdateOrganizationController;
use App\Presentation\Http\Controller\Web\User\CreateUserController;
use App\Presentation\Http\Controller\Web\User\DeleteUserController;
use App\Presentation\Http\Controller\Web\User\ListUsersController;
use App\Presentation\Http\Controller\Web\User\ShowCreateUserController;
use App\Presentation\Http\Controller\Web\User\ShowEditUserController;
use App\Presentation\Http\Controller\Web\User\UpdateUserController;
use Illuminate\Support\Facades\Route;

Route::post('/locale', SwitchLocaleController::class)->name('locale.update');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', ShowLoginController::class)->name('login');
    Route::post('/login', LoginController::class)->middleware('throttle:login');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/', DashboardController::class);

    Route::get('/users', ListUsersController::class)->name('users.index');
    Route::get('/users/create', ShowCreateUserController::class)->name('users.create');
    Route::post('/users', CreateUserController::class)->name('users.store');
    Route::get('/users/{userId}/edit', ShowEditUserController::class)->name('users.edit');
    Route::put('/users/{userId}', UpdateUserController::class)->name('users.update');
    Route::delete('/users/{userId}', DeleteUserController::class)->name('users.destroy');
    Route::get('/users/{userId}/permissions', UserPermissionsController::class)->name('users.permissions');

    Route::get('/roles', WebListRolesController::class)->name('roles.index');
    Route::get('/roles/create', ShowCreateRoleController::class)->name('roles.create');
    Route::post('/roles', WebCreateRoleController::class)->name('roles.store');
    Route::get('/roles/{roleId}', ShowRoleController::class)->name('roles.show');
    Route::get('/roles/{roleId}/edit', ShowEditRoleController::class)->name('roles.edit');
    Route::put('/roles/{roleId}', WebUpdateRoleController::class)->name('roles.update');
    Route::delete('/roles/{roleId}', WebDeleteRoleController::class)->name('roles.destroy');

    Route::get('/organizations', ListOrganizationsController::class)->name('organizations.index');
    Route::get('/organizations/create', ShowCreateOrganizationController::class)->name('organizations.create');
    Route::post('/organizations', CreateOrganizationController::class)->name('organizations.store');
    Route::get('/organizations/{organizationId}', ShowOrganizationController::class)->name('organizations.show');
    Route::get('/organizations/{organizationId}/edit', ShowEditOrganizationController::class)->name('organizations.edit');
    Route::put('/organizations/{organizationId}', UpdateOrganizationController::class)->name('organizations.update');
    Route::delete('/organizations/{organizationId}', DeleteOrganizationController::class)->name('organizations.destroy');
    Route::post('/organizations/{organizationId}/members', ManageOrganizationMembersController::class)->name('organizations.members');

    Route::post('/switch-organization', SwitchOrganizationController::class)->name('organizations.switch');

    Route::post('/impersonate/{userId}', WebStartImpersonationController::class)->name('impersonation.start');
    Route::post('/stop-impersonation', WebStopImpersonationController::class)->name('impersonation.stop');
});
