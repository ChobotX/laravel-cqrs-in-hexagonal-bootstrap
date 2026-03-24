<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Web\Auth\LoginController;
use App\Presentation\Http\Controller\Web\Auth\LogoutController;
use App\Presentation\Http\Controller\Web\Auth\ShowLoginController;
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
use App\Presentation\Http\Controller\Web\Authorization\UserPermissionsController;
use App\Presentation\Http\Controller\Web\DashboardController;
use App\Presentation\Http\Controller\Web\Locale\SwitchLocaleController;
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
    Route::post('/users/{userId}/permissions', ManageUserPermissionsController::class)->name('users.permissions.manage');

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

    Route::post('/impersonate/{userId}', WebStartImpersonationController::class)->name('impersonation.start');
    Route::post('/stop-impersonation', WebStopImpersonationController::class)->name('impersonation.stop');
});
