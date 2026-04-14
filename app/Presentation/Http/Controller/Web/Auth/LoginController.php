<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Domain\User\Contract\Query\GetPasswordRotationStatusQuery;
use App\Domain\User\Contract\Query\GetTwoFactorStatusQuery;
use App\Domain\User\Contract\ValueObject\PasswordRotationUiStatus;
use App\Presentation\Http\Request\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

#[SkipPermissionCheck('Guest login endpoint')]
final readonly class LoginController
{
    public function __invoke(LoginRequest $loginRequest, QueryBus $queryBus): RedirectResponse
    {
        $credentials = $loginRequest->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => __('messages.auth.invalid_credentials')])->onlyInput('email');
        }

        $loginRequest->session()->regenerate();

        $redirectResponse = redirect()->intended('/users');

        $passwordRotationUiStatus = $queryBus->dispatch(new GetPasswordRotationStatusQuery);

        if ($passwordRotationUiStatus->value === PasswordRotationUiStatus::WARNING) {
            return $redirectResponse->with('password_rotation', PasswordRotationUiStatus::WARNING);
        }

        if ($passwordRotationUiStatus->value === PasswordRotationUiStatus::EXPIRED) {
            return $redirectResponse->with('password_rotation', PasswordRotationUiStatus::EXPIRED);
        }

        $twoFactorUiStatus = $queryBus->dispatch(new GetTwoFactorStatusQuery);

        if ($twoFactorUiStatus->required) {
            session(['two_factor_passed' => false]);

            if ($twoFactorUiStatus->requiresSetup) {
                return redirect()->route('profile.two-factor');
            }

            return redirect()->route('two-factor.challenge');
        }

        session(['two_factor_passed' => true]);

        return $redirectResponse;
    }
}
