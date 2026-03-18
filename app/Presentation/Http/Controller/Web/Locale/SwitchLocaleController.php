<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Locale;

use App\Application\Authorization\SkipPermissionCheck;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

#[SkipPermissionCheck('Available to all users')]
final readonly class SwitchLocaleController
{
    public function __construct(
        private Repository $repository,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $locale = $request->string('locale')->toString();

        /** @var list<string> $allowedLocales */
        $allowedLocales = $this->repository->get('app.locales', []);

        if (in_array($locale, $allowedLocales, true)) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->back();
    }
}
