<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Application\Authorization\SkipPermissionCheck;
use Illuminate\View\View;

#[SkipPermissionCheck('Guest login page')]
final readonly class ShowLoginController
{
    public function __invoke(): View
    {
        return view('auth.login');
    }
}
