<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Domain\FeatureFlag\Contract\Enum\FlagType;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagCacheInvalidator;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagChecker;
use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Domain\FeatureFlag\Service\DefaultFeatureFlagChecker;
use App\Domain\Tenancy\Contract\Service\TenantContext;
use App\Infrastructure\Eloquent\FeatureFlag\EloquentFeatureFlagOverrideRepository;
use App\Infrastructure\FeatureFlag\CachedFeatureFlagChecker;
use App\Infrastructure\FeatureFlag\CacheFeatureFlagCacheInvalidator;
use App\Infrastructure\FeatureFlag\ConfigFeatureFlagDefinitionProvider;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;
use Override;

final class FeatureFlagServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/feature-flags.php',
            'feature-flags',
        );

        $this->app->singleton(FeatureFlagDefinitionProvider::class, function (): ConfigFeatureFlagDefinitionProvider {
            /** @var array<string, array{label: string, flags: array<string, array{type: string, default: bool|string, label: string, description: string, options?: list<string>, pattern?: string}>}> $groups */
            $groups = config('feature-flags.groups');

            return new ConfigFeatureFlagDefinitionProvider($groups);
        });

        $this->app->bind(FeatureFlagOverrideRepository::class, EloquentFeatureFlagOverrideRepository::class);

        $this->app->bind(FeatureFlagChecker::class, function (): CachedFeatureFlagChecker {
            /** @var int $ttl */
            $ttl = config('feature-flags.cache_ttl');

            $tenantContext = $this->app->make(TenantContext::class);

            return new CachedFeatureFlagChecker(
                new DefaultFeatureFlagChecker(
                    $this->app->make(FeatureFlagDefinitionProvider::class),
                    $this->app->make(FeatureFlagOverrideRepository::class),
                    $tenantContext,
                ),
                $this->app->make(CacheRepository::class),
                $tenantContext,
                $ttl,
            );
        });

        $this->app->bind(FeatureFlagCacheInvalidator::class, CacheFeatureFlagCacheInvalidator::class);
    }

    public function boot(): void
    {
        Blade::if('feature', function (string $key): bool {
            $featureFlagChecker = $this->app->make(FeatureFlagChecker::class);

            return $featureFlagChecker->isEnabled($key);
        });

        Blade::if('featureValue', function (string $key, string $expected): bool {
            $featureFlagChecker = $this->app->make(FeatureFlagChecker::class);

            return $featureFlagChecker->value($key) === $expected;
        });

        View::share('flagTypeBoolean', FlagType::Boolean);
        View::share('flagTypeSelect', FlagType::Select);
        View::share('flagTypeInput', FlagType::Input);

        View::composer('layouts.app', function (ViewInstance $viewInstance): void {
            $featureFlagChecker = $this->app->make(FeatureFlagChecker::class);
            $viewInstance->with('featureFlags', $featureFlagChecker->all());
        });
    }
}
