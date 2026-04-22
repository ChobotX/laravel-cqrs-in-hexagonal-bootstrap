<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Root;

use App\Contract\Attribute\SkipPermissionCheck;
use Illuminate\View\View;

#[SkipPermissionCheck('Public tenant registration form')]
final readonly class ShowRegisterTenantController
{
    public function __invoke(): View
    {
        return view('root.register');
    }
}
