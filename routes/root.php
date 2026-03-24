<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\Web\Root\LandingController;
use App\Presentation\Http\Controller\Web\Root\RegisterTenantController;
use App\Presentation\Http\Controller\Web\Root\ShowRegisterTenantController;
use App\Presentation\Http\Middleware\EnsureTenantResolved;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(EnsureTenantResolved::class)->group(function (): void {
    Route::get('/', LandingController::class)->name('root.landing');
    Route::get('/register', ShowRegisterTenantController::class)->name('root.register');
    Route::post('/register', RegisterTenantController::class)->name('root.register.store');
});
