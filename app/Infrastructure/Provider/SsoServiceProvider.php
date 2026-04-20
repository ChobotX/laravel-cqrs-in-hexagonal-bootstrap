<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\Sso\Contract\Service\SsoAuthenticatorRegistry as RegistryContract;
use App\Infrastructure\Eloquent\Sso\EloquentSsoConfigurationRepository;
use App\Infrastructure\Eloquent\Sso\EloquentUserSsoIdentityRepository;
use App\Infrastructure\Sso\SsoAuthenticatorRegistry;
use Illuminate\Support\ServiceProvider;
use Override;

final class SsoServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(SsoConfigurationRepository::class, EloquentSsoConfigurationRepository::class);
        $this->app->singleton(UserSsoIdentityRepository::class, EloquentUserSsoIdentityRepository::class);
        $this->app->singleton(RegistryContract::class, SsoAuthenticatorRegistry::class);
    }
}
