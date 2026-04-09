<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use App\Infrastructure\Auth\EloquentPasswordManager;
use App\Infrastructure\Auth\LaravelInviteLinkGenerator;
use App\Infrastructure\Auth\LaravelPasswordResetBroker;
use App\Infrastructure\Auth\SoftDeleteAwareUserProvider;
use App\Infrastructure\Auth\UserProviderNotFoundException;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Override;

final class AuthServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(PasswordManager::class, EloquentPasswordManager::class);
        $this->app->bind(InviteLinkGenerator::class, LaravelInviteLinkGenerator::class);
        $this->app->bind(PasswordResetBroker::class, LaravelPasswordResetBroker::class);
        $this->app->bind(UserProvider::class, fn (): UserProvider => Auth::createUserProvider('users')
            ?? throw new UserProviderNotFoundException('users'));
    }

    public function boot(): void
    {
        Auth::provider('soft-delete-aware', function (\Illuminate\Contracts\Foundation\Application $application, array $config): SoftDeleteAwareUserProvider {
            /** @var string $model */
            $model = $config['model'];

            return new SoftDeleteAwareUserProvider(
                $application->make(Hasher::class),
                $model,
            );
        });
    }
}
