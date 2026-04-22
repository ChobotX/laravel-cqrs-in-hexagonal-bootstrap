<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Middleware;

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\ScopeAwareQuery;
use App\Application\Authorization\ScopeTarget;
use App\Application\Authorization\ShareableScopeQuery;
use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\BusMiddleware;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Team\Contract\Service\TeamMembershipChecker;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Closure;
use ReflectionClass;

final readonly class ResolveScopeFilter implements BusMiddleware
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    /**
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
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
            AccessScope::Own => match ($message->scopeTarget()) {
                ScopeTarget::User => [$userId],
                ScopeTarget::Team => [],
            },
            AccessScope::Team => match ($message->scopeTarget()) {
                ScopeTarget::User => $this->teamMembershipChecker->directVisibleUserIds($userId),
                ScopeTarget::Team => $this->teamMembershipChecker->directMemberTeamIds($userId),
            },
            AccessScope::TeamTree => match ($message->scopeTarget()) {
                ScopeTarget::User => $this->teamMembershipChecker->visibleUserIds($userId),
                ScopeTarget::Team => $this->teamMembershipChecker->memberTeamIds($userId),
            },
        };

        $sharedResourceIds = null;
        if ($message instanceof ShareableScopeQuery && $accessScope !== AccessScope::All) {
            $sharedResourceIds = $this->authorizationChecker->accessibleResourceIds(
                $userId,
                $message->shareableResourceType(),
                Action::Read->value,
            );
        }

        $accessContext = new AccessContext($accessScope, $visibleIds, $sharedResourceIds);

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
