<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Presentation\Http\Request\Auth\LogoutRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

#[SkipPermissionCheck('Available to all authenticated users')]
final readonly class LogoutController
{
    public function __invoke(LogoutRequest $logoutRequest): RedirectResponse
    {
        Auth::logout();

        $logoutRequest->session()->invalidate();
        $logoutRequest->session()->regenerateToken();

        return redirect('/login');
    }
}
