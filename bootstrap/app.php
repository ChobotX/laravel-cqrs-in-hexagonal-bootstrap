<?php

declare(strict_types=1);

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use App\Domain\User\Contract\Exception\PasswordPreviouslyUsedException;
use App\Presentation\Http\Middleware\CheckPermission;
use App\Presentation\Http\Middleware\EnforcePasswordRotation;
use App\Presentation\Http\Middleware\EnsureTenantResolved;
use App\Presentation\Http\Middleware\ResolveTenantMiddleware;
use App\Presentation\Http\Middleware\SetAuthContextMiddleware;
use App\Presentation\Http\Middleware\SetLocaleMiddleware;
use App\Presentation\Http\Middleware\SetTraceIdMiddleware;
use App\Presentation\Http\Security\SafeRedirectValidator;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Infrastructure\Provider\AppServiceProvider::class,
        App\Infrastructure\Provider\TenancyServiceProvider::class,
        App\Infrastructure\Provider\FeatureFlagServiceProvider::class,
    ])
    ->withCommands([
        __DIR__.'/../app/Presentation/Console/User',
        __DIR__.'/../app/Presentation/Console/Tenancy',
        __DIR__.'/../app/Infrastructure/Console/Tenancy',
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')
                ->group(base_path('routes/internal_api.php'));

            Route::middleware('web')
                ->group(base_path('routes/root.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(SetTraceIdMiddleware::class);
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', ''));
        $middleware->redirectTo(
            guests: function (Request $request): string {
                if (! $request->isMethod('GET')) {
                    return '/login';
                }

                return '/login?'.http_build_query(['redirect' => $request->getRequestUri()]);
            },
            users: '/dashboard',
        );
        $middleware->web(prepend: [ResolveTenantMiddleware::class, EnsureTenantResolved::class]);
        $middleware->web(append: [SetLocaleMiddleware::class, SetAuthContextMiddleware::class, EnforcePasswordRotation::class, CheckPermission::class, 'throttle:web']);
        $middleware->priority([
            ResolveTenantMiddleware::class,
            EnsureTenantResolved::class,
            Illuminate\Auth\Middleware\Authenticate::class,
            SetAuthContextMiddleware::class,
            EnforcePasswordRotation::class,
            CheckPermission::class,
        ]);
        $middleware->api(prepend: [ResolveTenantMiddleware::class, EnsureTenantResolved::class]);
        $middleware->api(append: ['throttle:api']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->renderable(function (HttpException $e, Request $request): JsonResponse|RedirectResponse|null {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if (Auth::check()) {
                $back = url()->previous() ?: '/';
                $separator = str_contains($back, '?') ? '&' : '?';

                return redirect($back.$separator.'csrf_expired=1')->withInput();
            }

            if ($request->expectsJson()) {
                return new JsonResponse(
                    ['message' => __('messages.auth.session_expired'), 'redirect' => '/login'],
                    419,
                );
            }

            $referer = $request->headers->get('referer');
            $loginUrl = '/login';

            if ($referer !== null && SafeRedirectValidator::isSafe($referer, $request->getHost())) {
                $loginUrl = '/login?'.http_build_query(['redirect' => $referer]);
            }

            return redirect($loginUrl);
        });

        $exceptions->renderable(function (Throwable $e, Request $request): JsonResponse|Response|null {
            if (! $e instanceof DomainException) {
                return null;
            }

            $translator = app(Translator::class);
            $statusCode = $e->statusCode();

            if ($request->expectsJson()) {
                return new JsonResponse(
                    [
                        'message' => $e->userMessage($translator),
                        'trace_id' => Context::get('trace_id'),
                    ],
                    $statusCode,
                );
            }

            if ($statusCode === Response::HTTP_FORBIDDEN) {
                throw new AccessDeniedHttpException($e->userMessage($translator), $e);
            }

            if ($statusCode === Response::HTTP_NOT_FOUND) {
                throw new NotFoundHttpException($e->userMessage($translator), $e);
            }

            if ($e instanceof PasswordPreviouslyUsedException) {
                return redirect()->back()->withErrors(['password' => $e->userMessage($translator)]);
            }

            return redirect()->back()->withErrors(['message' => $e->userMessage($translator)]);
        });
    })->create();
