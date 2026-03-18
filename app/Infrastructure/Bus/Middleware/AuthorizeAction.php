<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus\Middleware;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Bus\Middleware;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Exception\PermissionDeniedException;
use Closure;
use ReflectionClass;

final readonly class AuthorizeAction implements Middleware
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private OrganizationContext $organizationContext,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function handle(object $message, Closure $next): mixed
    {
        $reflectionClass = new ReflectionClass($message);
        $requiresPermission = $reflectionClass->getAttributes(RequiresPermission::class);

        if ($requiresPermission === []) {
            return $next($message);
        }

        $attribute = $requiresPermission[0]->newInstance();

        $userId = $this->authenticatedUser->id();

        if ($userId === null) {
            return $next($message);
        }

        $organizationId = $this->organizationContext->currentOrganizationId();

        if ($organizationId === null) {
            throw new PermissionDeniedException($attribute->permission);
        }

        if (! $this->authorizationChecker->can($userId, $organizationId, $attribute->permission)) {
            throw new PermissionDeniedException($attribute->permission);
        }

        return $next($message);
    }
}
