<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Contract\Tenancy\TenantBootstrapper;
use App\Contract\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class ResolveTenantMiddleware
{
    public function __construct(
        private TenantBootstrapper $tenantBootstrapper,
        private TenantContext $tenantContext,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        /** @var string $rootDomain */
        $rootDomain = config('tenancy.root_domain');

        $subdomain = str_replace('.'.$rootDomain, '', $host);

        if (in_array($subdomain, [$host, '', 'www'], true)) {
            return $next($request);
        }

        try {
            $this->tenantBootstrapper->bootstrapByDomain($subdomain);
        } catch (NotFoundHttpException) {
            abort(404);
        }

        Context::add('tenant_id', $this->tenantContext->currentTenantId());
        Context::add('tenant_slug', $this->tenantContext->currentTenantSlug());

        return $next($request);
    }
}
