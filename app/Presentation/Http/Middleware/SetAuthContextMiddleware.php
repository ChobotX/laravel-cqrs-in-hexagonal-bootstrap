<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Domain\User\Contract\Service\AuthenticatedUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final readonly class SetAuthContextMiddleware
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $this->authenticatedUser->id();

        if ($userId !== null) {
            Context::add('user_id', $userId);

            $impersonatorId = $this->authenticatedUser->impersonatorId();

            if ($impersonatorId !== null) {
                Context::add('impersonator_id', $impersonatorId);
            }
        }

        View::share('authenticatedUser', $this->authenticatedUser);

        return $next($request);
    }
}
