<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web;

use App\Application\Authorization\SkipPermissionCheck;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('Available to all authenticated users')]
final readonly class DashboardController
{
    public function __invoke(): RedirectResponse
    {
        return redirect('/users');
    }
}
