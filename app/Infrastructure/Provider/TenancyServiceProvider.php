<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\Tenancy\TenantContext;
use App\Contract\Tenancy\TenantLogoStorage;
use App\Domain\Tenancy\Contract\Repository\TenantMailTransportRepository;
use App\Domain\Tenancy\Contract\Repository\TenantSettingsRepository;
use App\Domain\Tenancy\Contract\Service\DevSchemaResetter;
use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use App\Domain\Tenancy\Contract\Service\TenantProvisioner;
use App\Infrastructure\Dev\DevSchemaResetter as DevSchemaResetterImpl;
use App\Infrastructure\File\TenantLogoFileStorage;
use App\Infrastructure\Tenancy\ConsoleTenantBootstrap;
use App\Infrastructure\Tenancy\EloquentTenantMailTransportRepository;
use App\Infrastructure\Tenancy\EloquentTenantProvisioner;
use App\Infrastructure\Tenancy\EloquentTenantSettingsRepository;
use App\Infrastructure\Tenancy\ResolvedTenantContext;
use App\Infrastructure\Tenancy\TenantBootstrapperImpl;
use App\Infrastructure\Tenancy\TenantMigrator;
use App\Infrastructure\Tenancy\TenantResolver;
use App\Infrastructure\Tenancy\TenantSchemaManager;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Filesystem\FilesystemManager;
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
        $this->app->bind(TenantLogoStorage::class, fn (): TenantLogoFileStorage => new TenantLogoFileStorage(
            filesystem: $this->app->make(FilesystemManager::class)->disk('public'),
        ));
        $this->app->bind(TenantSettingsRepository::class, EloquentTenantSettingsRepository::class);
        $this->app->bind(TenantMailTransportRepository::class, EloquentTenantMailTransportRepository::class);
        $this->app->bind(DevSchemaResetter::class, DevSchemaResetterImpl::class);
    }

    public function boot(): void
    {
        Event::listen(CommandStarting::class, ConsoleTenantBootstrap::class);
    }
}
