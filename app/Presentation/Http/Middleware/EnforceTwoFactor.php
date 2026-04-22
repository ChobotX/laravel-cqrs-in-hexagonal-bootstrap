<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Contract\Bus\QueryBus;
use App\Domain\User\Contract\Query\GetTwoFactorStatusQuery;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnforceTwoFactor
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        if ($request->routeIs(
            'two-factor.challenge',
            'two-factor.verify',
            'two-factor.email-code',
            'profile.two-factor',
            'profile.two-factor.backup-codes.download',
            'profile.two-factor.update',
            'logout',
            'locale.update',
        )) {
            return $next($request);
        }

        if (! $request->session()->has('two_factor_passed')) {
            return $next($request);
        }

        if ((bool) $request->session()->get('two_factor_passed')) {
            return $next($request);
        }

        $twoFactorUiStatus = $this->queryBus->dispatch(new GetTwoFactorStatusQuery);

        if (! $twoFactorUiStatus->required) {
            return $next($request);
        }

        if ($twoFactorUiStatus->requiresSetup) {
            return redirect()->route('profile.two-factor');
        }

        return redirect()->route('two-factor.challenge');
    }
}
