<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class SetLocaleMiddleware
{
    public function __construct(
        private Application $application,
        private Repository $repository,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        /** @var list<string> $allowedLocales */
        $allowedLocales = $this->repository->get('app.locales', []);

        if (is_string($locale) && in_array($locale, $allowedLocales, true)) {
            $this->application->setLocale($locale);
        }

        return $next($request);
    }
}
