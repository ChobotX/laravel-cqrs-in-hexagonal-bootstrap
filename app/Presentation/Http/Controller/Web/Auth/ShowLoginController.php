<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Application\Authorization\SkipPermissionCheck;
use App\Presentation\Http\Request\Auth\ShowLoginRequest;
use App\Presentation\Http\Security\SafeRedirectValidator;
use Illuminate\View\View;

#[SkipPermissionCheck('Guest login page')]
final readonly class ShowLoginController
{
    public function __invoke(ShowLoginRequest $showLoginRequest): View
    {
        $redirect = $showLoginRequest->validated('redirect');

        if (is_string($redirect) && SafeRedirectValidator::isSafe($redirect, $showLoginRequest->getHost())) {
            $showLoginRequest->session()->put('url.intended', $redirect);
        }

        return view('auth.login');
    }
}
