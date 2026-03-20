<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Web\Authorization\SearchRolesController;
use App\Presentation\Http\Controller\Web\Organization\SearchOrganizationsController;
use App\Presentation\Http\Controller\Web\User\SearchUsersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('internal-api')->group(function (): void {
    Route::get('/users/search', SearchUsersController::class)->name('internal-api.users.search');
    Route::get('/roles/search', SearchRolesController::class)->name('internal-api.roles.search');
    Route::get('/organizations/search', SearchOrganizationsController::class)->name('internal-api.organizations.search');
});
