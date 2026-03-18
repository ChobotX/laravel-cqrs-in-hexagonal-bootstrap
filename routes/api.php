<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Authorization\AssignUserRoleController;
use App\Presentation\Http\Controller\Authorization\CreateRoleController;
use App\Presentation\Http\Controller\Authorization\DeleteRoleController;
use App\Presentation\Http\Controller\Authorization\GetEffectivePermissionsController;
use App\Presentation\Http\Controller\Authorization\GetRoleController;
use App\Presentation\Http\Controller\Authorization\GetUserPermissionsController;
use App\Presentation\Http\Controller\Authorization\ListRolesController;
use App\Presentation\Http\Controller\Authorization\ListUserRolesController;
use App\Presentation\Http\Controller\Authorization\RemoveUserPermissionOverrideController;
use App\Presentation\Http\Controller\Authorization\RevokeUserRoleController;
use App\Presentation\Http\Controller\Authorization\SetUserPermissionOverrideController;
use App\Presentation\Http\Controller\Authorization\StartImpersonationController;
use App\Presentation\Http\Controller\Authorization\StopImpersonationController;
use App\Presentation\Http\Controller\Authorization\UpdateRoleController;
use App\Presentation\Http\Controller\User\CreateUserController;
use App\Presentation\Http\Controller\User\DeleteUserController;
use App\Presentation\Http\Controller\User\GetUserController;
use App\Presentation\Http\Controller\User\ListUsersController;
use App\Presentation\Http\Controller\User\UpdateUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/users', ListUsersController::class);
    Route::post('/users', CreateUserController::class);
    Route::get('/users/{userId}', GetUserController::class);
    Route::put('/users/{userId}', UpdateUserController::class);
    Route::delete('/users/{userId}', DeleteUserController::class);

    Route::get('/roles', ListRolesController::class);
    Route::post('/roles', CreateRoleController::class);
    Route::get('/roles/{roleId}', GetRoleController::class);
    Route::put('/roles/{roleId}', UpdateRoleController::class);
    Route::delete('/roles/{roleId}', DeleteRoleController::class);

    Route::get('/users/{userId}/roles', ListUserRolesController::class);
    Route::post('/users/{userId}/roles', AssignUserRoleController::class);
    Route::delete('/users/{userId}/roles/{roleId}', RevokeUserRoleController::class);

    Route::get('/users/{userId}/permissions', GetUserPermissionsController::class);
    Route::put('/users/{userId}/permissions', SetUserPermissionOverrideController::class);
    Route::delete('/users/{userId}/permissions/{permission}', RemoveUserPermissionOverrideController::class);

    Route::get('/users/{userId}/effective-permissions', GetEffectivePermissionsController::class);

    Route::post('/impersonate/{userId}', StartImpersonationController::class);
    Route::post('/stop-impersonation', StopImpersonationController::class);
});
