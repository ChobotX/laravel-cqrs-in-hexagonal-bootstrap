<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Api\V1\Authorization\AssignUserRoleController;
use App\Presentation\Http\Controller\Api\V1\Authorization\CreateRoleController;
use App\Presentation\Http\Controller\Api\V1\Authorization\DeleteRoleController;
use App\Presentation\Http\Controller\Api\V1\Authorization\GetEffectivePermissionsController;
use App\Presentation\Http\Controller\Api\V1\Authorization\GetRoleController;
use App\Presentation\Http\Controller\Api\V1\Authorization\GetUserPermissionsController;
use App\Presentation\Http\Controller\Api\V1\Authorization\ListRolesController;
use App\Presentation\Http\Controller\Api\V1\Authorization\ListUserRolesController;
use App\Presentation\Http\Controller\Api\V1\Authorization\RemoveUserPermissionOverrideController;
use App\Presentation\Http\Controller\Api\V1\Authorization\RevokeUserRoleController;
use App\Presentation\Http\Controller\Api\V1\Authorization\SetUserPermissionOverrideController;
use App\Presentation\Http\Controller\Api\V1\Authorization\StartImpersonationController;
use App\Presentation\Http\Controller\Api\V1\Authorization\StopImpersonationController;
use App\Presentation\Http\Controller\Api\V1\Authorization\UpdateRoleController;
use App\Presentation\Http\Controller\Api\V1\User\CreateUserController;
use App\Presentation\Http\Controller\Api\V1\User\DeleteUserController;
use App\Presentation\Http\Controller\Api\V1\User\GetUserController;
use App\Presentation\Http\Controller\Api\V1\User\ListUsersController;
use App\Presentation\Http\Controller\Api\V1\User\UpdateUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function (): void {
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
