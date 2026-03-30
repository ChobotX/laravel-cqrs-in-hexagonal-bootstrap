<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Middleware;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\ScopeAwareQuery;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Bus\Middleware;
use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Authorization\AccessScope;
use Closure;
use ReflectionClass;

final readonly class ResolveScopeFilter implements Middleware
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    public function handle(object $message, Closure $next): mixed
    {
        if (! $message instanceof ScopeAwareQuery) {
            return $next($message);
        }

        $userId = $this->authenticatedUser->id();

        if ($userId === null) {
            return $next($message);
        }

        $permission = $this->extractPermission($message);

        if ($permission === null) {
            return $next($message);
        }

        $accessDecision = $this->authorizationChecker->canWithScope($userId, $permission);
        $accessScope = AccessScope::from($accessDecision->scope());

        $visibleIds = match ($accessScope) {
            AccessScope::All => null,
            AccessScope::Own => [$userId],
            AccessScope::Team => $this->teamMembershipChecker->visibleUserIds($userId),
        };

        $accessContext = new AccessContext($accessScope, $visibleIds);

        return $next($message->withAccessContext($accessContext));
    }

    private function extractPermission(object $message): ?string
    {
        $reflectionClass = new ReflectionClass($message);
        $attributes = $reflectionClass->getAttributes(RequiresPermission::class);

        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance()->permission;
    }
}
