<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Contract\Http\HttpStatus;
use App\Domain\Tenancy\Contract\Service\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureTenantResolved
{
    public function __construct(
        private TenantContext $tenantContext,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->tenantContext->isResolved()) {
            abort(HttpStatus::NOT_FOUND);
        }

        return $next($request);
    }
}
