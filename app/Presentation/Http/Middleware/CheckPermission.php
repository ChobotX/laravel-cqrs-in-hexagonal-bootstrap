<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Exception\PermissionDeniedException;
use Closure;
use Illuminate\Http\Request;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;

final readonly class CheckPermission
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private OrganizationContext $organizationContext,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $controller = $request->route()?->getControllerClass();

        if ($controller === null || ! class_exists($controller)) {
            return $next($request);
        }

        $reflectionClass = new ReflectionClass($controller);
        $attributes = $reflectionClass->getAttributes(RequiresPermission::class);

        if ($attributes === []) {
            return $next($request);
        }

        $permission = $attributes[0]->newInstance()->permission;
        $userId = $this->authenticatedUser->id() ?? '';
        $orgId = $this->organizationContext->currentOrganizationId();

        if ($orgId === null || ! $this->authorizationChecker->can($userId, $orgId, $permission)) {
            throw new PermissionDeniedException($permission);
        }

        return $next($request);
    }
}
