<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\Tenancy\TenantBootstrapper;
use App\Contract\Tenancy\TenantContext;
use App\Domain\Tenancy\Contract\Service\TenantProvisioner;
use App\Infrastructure\Tenancy\ConsoleTenantBootstrap;
use App\Infrastructure\Tenancy\EloquentTenantProvisioner;
use App\Infrastructure\Tenancy\ResolvedTenantContext;
use App\Infrastructure\Tenancy\TenantBootstrapperImpl;
use App\Infrastructure\Tenancy\TenantMigrator;
use App\Infrastructure\Tenancy\TenantResolver;
use App\Infrastructure\Tenancy\TenantSchemaManager;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Override;

final class TenancyServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/tenancy.php',
            'tenancy',
        );

        $this->app->scoped(ResolvedTenantContext::class);
        $this->app->bind(TenantContext::class, ResolvedTenantContext::class);
        $this->app->bind(TenantBootstrapper::class, TenantBootstrapperImpl::class);
        $this->app->singleton(TenantSchemaManager::class);
        $this->app->singleton(TenantResolver::class);
        $this->app->singleton(TenantMigrator::class);
        $this->app->bind(TenantProvisioner::class, EloquentTenantProvisioner::class);
    }

    public function boot(): void
    {
        Event::listen(CommandStarting::class, ConsoleTenantBootstrap::class);
    }
}
