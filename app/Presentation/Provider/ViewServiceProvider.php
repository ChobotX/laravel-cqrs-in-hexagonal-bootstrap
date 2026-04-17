<?php

declare(strict_types=1);

namespace App\Presentation\Provider;

use App\Presentation\View\Sidebar\SidebarNavigationBuilder;
use App\Presentation\View\Sidebar\SidebarNavigationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Override;

final class ViewServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(SidebarNavigationBuilder::class);
    }

    public function boot(): void
    {
        View::composer('components.sidebar-nav', SidebarNavigationComposer::class);
    }
}
