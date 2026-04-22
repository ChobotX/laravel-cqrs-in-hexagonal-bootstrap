<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Contract\Attribute\SkipPermissionCheck;
use Illuminate\View\View;

#[SkipPermissionCheck('Guest forgot password page')]
final readonly class ShowForgotPasswordController
{
    public function __invoke(): View
    {
        return view('auth.forgot-password');
    }
}
