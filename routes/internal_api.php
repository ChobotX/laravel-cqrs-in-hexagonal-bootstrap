<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Web\Authorization\SearchRolesController;
use App\Presentation\Http\Controller\Web\Label\CreateLabelController;
use App\Presentation\Http\Controller\Web\Label\SearchLabelsController;
use App\Presentation\Http\Controller\Web\Team\GetTeamTreeController;
use App\Presentation\Http\Controller\Web\Team\SearchTeamsController;
use App\Presentation\Http\Controller\Web\User\SearchUsersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('internal-api')->group(function (): void {
    Route::get('/users/search', SearchUsersController::class)->name('internal-api.users.search');
    Route::get('/roles/search', SearchRolesController::class)->name('internal-api.roles.search');
    Route::get('/teams/search', SearchTeamsController::class)->name('internal-api.teams.search');
    Route::get('/teams/tree', GetTeamTreeController::class)->name('internal-api.teams.tree');
    Route::get('/labels/search', SearchLabelsController::class)->name('internal-api.labels.search');
    Route::post('/labels', CreateLabelController::class)->name('internal-api.labels.create');
});
