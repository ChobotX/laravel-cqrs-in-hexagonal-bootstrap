<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Middleware;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Auth\AuthorizationChecker;
use App\Contract\Bus\BusMiddleware;
use App\Domain\Authorization\Contract\Exception\PermissionDeniedException;
use Closure;
use ReflectionClass;

final readonly class AuthorizeAction implements BusMiddleware
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    /**
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
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

        if (! $this->authorizationChecker->can($userId, $attribute->permission)) {
            throw new PermissionDeniedException($attribute->permission);
        }

        return $next($message);
    }
}
