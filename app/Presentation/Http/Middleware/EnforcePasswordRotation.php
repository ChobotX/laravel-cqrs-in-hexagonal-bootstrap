<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use App\Application\Bus\QueryBus;
use App\Domain\User\Contract\Query\GetPasswordRotationStatusQuery;
use App\Domain\User\Contract\ValueObject\PasswordRotationUiStatus;
use App\Presentation\Http\Request\Web\Settings\ShowTenantSettingsRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnforcePasswordRotation
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

        $passwordRotationUiStatus = $this->queryBus->dispatch(new GetPasswordRotationStatusQuery);

        if ($passwordRotationUiStatus->value !== PasswordRotationUiStatus::EXPIRED) {
            return $next($request);
        }

        if ($request->routeIs(
            'profile',
            'profile.update',
            'logout',
            'locale.update',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'settings.password-rotation',
            'settings.password-rotation.update',
        ) || $this->isPasswordRotationTabRequest($request)) {
            return $next($request);
        }

        return redirect()->route('profile')->with('password_rotation', PasswordRotationUiStatus::EXPIRED);
    }

    private function isPasswordRotationTabRequest(Request $request): bool
    {
        $tab = $request->query('tab');

        return $request->routeIs('settings.index')
            && is_string($tab)
            && $tab === ShowTenantSettingsRequest::PASSWORD_ROTATION_TAB;
    }
}
