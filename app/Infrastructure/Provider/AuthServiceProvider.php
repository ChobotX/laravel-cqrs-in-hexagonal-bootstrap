<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Domain\User\Contract\Service\InviteLinkGenerator;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use App\Infrastructure\User\EloquentPasswordManager;
use App\Infrastructure\User\LaravelInviteLinkGenerator;
use App\Infrastructure\User\LaravelPasswordResetBroker;
use App\Infrastructure\User\SoftDeleteAwareUserProvider;
use App\Infrastructure\User\UserProviderNotFoundException;
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
