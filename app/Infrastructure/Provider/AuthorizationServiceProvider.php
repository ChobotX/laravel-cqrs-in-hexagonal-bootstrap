<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Authorization\ImpersonationManager;
use App\Contract\Organization\OrganizationContext;
use App\Contract\Organization\OrganizationMembershipChecker;
use App\Contract\Organization\TeamMembershipChecker;
use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesHandler;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesHandler;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsHandler;
use App\Domain\Authorization\RecordShareRepository;
use App\Domain\Authorization\UserPermissionRepository;
use App\Infrastructure\Auth\RequestAuthenticatedUser;
use App\Infrastructure\Authorization\CachedAuthorizationChecker;
use App\Infrastructure\Authorization\ResolverAuthorizationChecker;
use App\Infrastructure\Authorization\SessionImpersonationManager;
use App\Infrastructure\Organization\CookieOrganizationContext;
use App\Infrastructure\Organization\EloquentOrganizationMembershipChecker;
use App\Infrastructure\Organization\EloquentTeamMembershipChecker;
use App\Presentation\Http\Controller\Web\User\ShowEditUserController;
use App\Presentation\Http\Controller\Web\User\UpdateUserController;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Blade;
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
                availableModules: $modules,
            );

            return new CachedAuthorizationChecker(
                authorizationChecker: $resolverAuthorizationChecker,
                cacheRepository: $this->app->make(CacheRepository::class),
                ttl: $ttl,
            );
        });

        $this->app->bind(AuthenticatedUser::class, RequestAuthenticatedUser::class);
        $this->app->bind(ImpersonationManager::class, SessionImpersonationManager::class);

        $this->app->bind(OrganizationContext::class, function (): CookieOrganizationContext {
            /** @var string|null $orgId */
            $orgId = config('authorization.default_organization_id');

            return new CookieOrganizationContext(
                organizationMembershipChecker: $this->app->make(OrganizationMembershipChecker::class),
                authenticatedUser: $this->app->make(AuthenticatedUser::class),
                defaultOrganizationId: $orgId,
            );
        });

        $this->app->bind(OrganizationMembershipChecker::class, EloquentOrganizationMembershipChecker::class);
        $this->app->bind(TeamMembershipChecker::class, EloquentTeamMembershipChecker::class);

        $this->app->when([GetAvailableModulesHandler::class, GetEffectivePermissionsHandler::class, SeedDefaultRolesHandler::class, ShowEditUserController::class, UpdateUserController::class])
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
        Blade::if('hasPermission', function (string $permission): bool {
            $authenticatedUser = $this->app->make(AuthenticatedUser::class);
            $organizationContext = $this->app->make(OrganizationContext::class);

            $userId = $authenticatedUser->id();

            if ($userId === null) {
                return false;
            }

            $organizationId = $organizationContext->currentOrganizationId();
            $authorizationChecker = $this->app->make(AuthorizationChecker::class);

            return $authorizationChecker->can($userId, $organizationId, $permission);
        });
    }
}
