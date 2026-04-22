<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Presentation\Http\Request\Auth\ShowResetPasswordRequest;
use Illuminate\View\View;

#[SkipPermissionCheck('Guest reset password page')]
final readonly class ShowResetPasswordController
{
    public function __invoke(ShowResetPasswordRequest $showResetPasswordRequest, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $showResetPasswordRequest->string('email')->toString(),
        ]);
    }
}
