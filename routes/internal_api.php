<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Api\Registry\ListDefinitionsApiController;
use App\Presentation\Http\Controller\Api\Registry\ListEntriesApiController;
use App\Presentation\Http\Controller\Api\Registry\ListNamespacesApiController;
use App\Presentation\Http\Controller\InternalApi\Authorization\ListRolesGridController;
use App\Presentation\Http\Controller\InternalApi\Registry\ListEntriesGridController;
use App\Presentation\Http\Controller\InternalApi\Team\ListTeamsGridController;
use App\Presentation\Http\Controller\InternalApi\User\ListUsersGridController;
use App\Presentation\Http\Controller\Web\Authorization\SearchRolesController;
use App\Presentation\Http\Controller\Web\GridPreset\ListGridPresetsApiController;
use App\Presentation\Http\Controller\Web\Label\CreateLabelController;
use App\Presentation\Http\Controller\Web\Label\SearchLabelsController;
use App\Presentation\Http\Controller\Web\Notification\CountUnreadNotificationsController;
use App\Presentation\Http\Controller\Web\Notification\DeleteNotificationController;
use App\Presentation\Http\Controller\Web\Notification\ListNotificationsController;
use App\Presentation\Http\Controller\Web\Notification\MarkAllNotificationsAsReadController;
use App\Presentation\Http\Controller\Web\Notification\MarkNotificationAsReadController;
use App\Presentation\Http\Controller\Web\Team\GetTeamTreeController;
use App\Presentation\Http\Controller\Web\Team\SearchTeamsController;
use App\Presentation\Http\Controller\Web\User\SearchUsersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('internal-api')->group(function (): void {
    Route::get('/users/list', ListUsersGridController::class)->name('internal-api.users.list');
    Route::get('/teams/list', ListTeamsGridController::class)->name('internal-api.teams.list');
    Route::get('/roles/list', ListRolesGridController::class)->name('internal-api.roles.list');
    Route::get('/registry/{namespace}/{slug}/entries/list', ListEntriesGridController::class)->name('internal-api.registry.entries.list');
    Route::get('/users/search', SearchUsersController::class)->name('internal-api.users.search');
    Route::get('/roles/search', SearchRolesController::class)->name('internal-api.roles.search');
    Route::get('/teams/search', SearchTeamsController::class)->name('internal-api.teams.search');
    Route::get('/teams/tree', GetTeamTreeController::class)->name('internal-api.teams.tree');
    Route::get('/labels/search', SearchLabelsController::class)->name('internal-api.labels.search');
    Route::post('/labels', CreateLabelController::class)->name('internal-api.labels.create');

    Route::get('/notifications', ListNotificationsController::class)->name('internal-api.notifications.index');
    Route::get('/notifications/unread-count', CountUnreadNotificationsController::class)->name('internal-api.notifications.unread-count');
    Route::post('/notifications/{notificationId}/mark-read', MarkNotificationAsReadController::class)->name('internal-api.notifications.mark-read');
    Route::post('/notifications/mark-all-read', MarkAllNotificationsAsReadController::class)->name('internal-api.notifications.mark-all-read');
    Route::delete('/notifications/{notificationId}', DeleteNotificationController::class)->name('internal-api.notifications.destroy');

    Route::get('/grid-presets', ListGridPresetsApiController::class)->name('internal-api.grid-presets.index');

    Route::get('/registry/definitions', ListDefinitionsApiController::class)->name('internal-api.registry.definitions');
    Route::get('/registry/namespaces', ListNamespacesApiController::class)->name('internal-api.registry.namespaces');
    Route::get('/registry/{namespace}/{slug}/entries', ListEntriesApiController::class)->name('internal-api.registry.entries');
});
