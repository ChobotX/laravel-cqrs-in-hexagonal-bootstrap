<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Contract\Http\HttpStatus;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Contract\Tenancy\TenantContext;
use App\Domain\Tenancy\Contract\Exception\InactiveTenantException;
use App\Domain\Tenancy\Contract\Exception\TenantNotFoundException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolveTenantMiddleware
{
    private const string WWW_SUBDOMAIN = 'www';

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

        $suffix = '.'.$rootDomain;

        if (! str_ends_with($host, $suffix)) {
            return $next($request);
        }

        $subdomain = substr($host, 0, -strlen($suffix));

        if ($subdomain === '' || $subdomain === self::WWW_SUBDOMAIN) {
            return $next($request);
        }

        try {
            $this->tenantBootstrapper->bootstrapByDomain($subdomain);
        } catch (TenantNotFoundException|InactiveTenantException) {
            abort(HttpStatus::NOT_FOUND);
        }

        $tenantSlug = $this->tenantContext->currentTenantSlug();

        Context::add('tenant_id', $this->tenantContext->currentTenantId());
        Context::add('tenant_slug', $tenantSlug);

        View::share('tenantSlug', $tenantSlug);

        return $next($request);
    }
}
