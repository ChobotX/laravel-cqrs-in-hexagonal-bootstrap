<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Locale;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Presentation\Http\Request\Web\Locale\SwitchLocaleRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('Available to all users')]
final readonly class SwitchLocaleController
{
    public function __invoke(SwitchLocaleRequest $switchLocaleRequest): RedirectResponse
    {
        $switchLocaleRequest->session()->put('locale', $switchLocaleRequest->locale());

        return redirect()->back();
    }
}
