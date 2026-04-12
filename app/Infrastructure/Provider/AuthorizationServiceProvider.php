<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Application\Authorization\ShareableResourceRegistry;
use App\Contract\Tenancy\TenantContext;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Repository\RecordShareRepository;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Authorization\Contract\Service\AuthorizationRefresher;
use App\Domain\Authorization\Contract\Service\ImpersonationManager;
use App\Domain\Authorization\Handler\Command\SeedDefaultRolesHandler;
use App\Domain\Authorization\Handler\Command\SyncUserRolesHandler;
use App\Domain\Authorization\Handler\Query\GetAssignableRolesHandler;
use App\Domain\Authorization\Handler\Query\GetAvailableModulesHandler;
use App\Domain\Authorization\Handler\Query\GetEffectivePermissionsHandler;
use App\Domain\Authorization\Handler\Query\GetOwnEffectivePermissionsHandler;
use App\Domain\Authorization\Service\PermissionResolver;
use App\Domain\Team\Contract\Service\TeamMembershipChecker;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Infrastructure\Auth\RequestAuthenticatedUser;
use App\Infrastructure\Authorization\CacheAuthorizationRefresher;
use App\Infrastructure\Authorization\CachedAuthorizationChecker;
use App\Infrastructure\Authorization\ResolverAuthorizationChecker;
use App\Infrastructure\Authorization\SessionImpersonationManager;
use App\Infrastructure\Team\CachedTeamMembershipChecker;
use App\Infrastructure\Team\EloquentTeamMembershipChecker;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Override;

final class AuthorizationServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/authorization.php',
            'authorization',
        );

        $this->app->bind(AuthorizationChecker::class, function (): CachedAuthorizationChecker {
            /** @var array<string, array{features: array<string, array{actions: list<string>}>}> $modules */
            $modules = config('authorization.modules');

            /** @var int $ttl */
            $ttl = config('authorization.cache_ttl');

            $resolverAuthorizationChecker = new ResolverAuthorizationChecker(
                userPermissionRepository: $this->app->make(UserPermissionRepository::class),
                recordShareRepository: $this->app->make(RecordShareRepository::class),
                permissionResolver: new PermissionResolver,
                shareableResourceRegistry: $this->app->make(ShareableResourceRegistry::class),
                availableModules: $modules,
            );

            return new CachedAuthorizationChecker(
                authorizationChecker: $resolverAuthorizationChecker,
                cacheRepository: $this->app->make(CacheRepository::class),
                tenantContext: $this->app->make(TenantContext::class),
                ttl: $ttl,
            );
        });

        $this->app->bind(AuthorizationRefresher::class, CacheAuthorizationRefresher::class);
        $this->app->bind(AuthenticatedUser::class, RequestAuthenticatedUser::class);
        $this->app->bind(ImpersonationManager::class, SessionImpersonationManager::class);

        $this->app->singleton(ShareableResourceRegistry::class, fn (): ShareableResourceRegistry => new ShareableResourceRegistry([
            'entry' => 'registry.entries',
        ]));

        $this->app->bind(EloquentTeamMembershipChecker::class);
        $this->app->scoped(TeamMembershipChecker::class, fn (): CachedTeamMembershipChecker => new CachedTeamMembershipChecker(
            $this->app->make(EloquentTeamMembershipChecker::class),
        ));

        $this->app->when([GetAvailableModulesHandler::class, GetEffectivePermissionsHandler::class, GetOwnEffectivePermissionsHandler::class, SeedDefaultRolesHandler::class, GetAssignableRolesHandler::class, SyncUserRolesHandler::class])
            ->needs('$availableModules')
            ->give(static function (): array {
                /** @var array<string, array{features: array<string, array{actions: list<string>}>}> $modules */
                $modules = config('authorization.modules');

                return $modules;
            });

        $this->app->when(GetAvailableModulesHandler::class)
            ->needs('$modules')
            ->give(static function (): array {
                /** @var array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}> $modules */
                $modules = config('authorization.modules');

                return $modules;
            });
    }

    public function boot(): void
    {
        View::share('accessScopes', AccessScope::cases());

        Blade::if('hasPermission', function (string $permission): bool {
            $authenticatedUser = $this->app->make(AuthenticatedUser::class);

            $userId = $authenticatedUser->id();

            if ($userId === null) {
                return false;
            }

            $authorizationChecker = $this->app->make(AuthorizationChecker::class);

            return $authorizationChecker->can($userId, $permission);
        });
    }
}
