<?php

declare(strict_types=1);

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use App\Presentation\Http\Middleware\CheckPermission;
use App\Presentation\Http\Middleware\ResolveTenantMiddleware;
use App\Presentation\Http\Middleware\SetAuthContextMiddleware;
use App\Presentation\Http\Middleware\SetLocaleMiddleware;
use App\Presentation\Http\Middleware\SetTraceIdMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Infrastructure\Provider\AppServiceProvider::class,
        App\Infrastructure\Provider\TenancyServiceProvider::class,
    ])
    ->withCommands([
        __DIR__.'/../app/Presentation/Console/User',
        __DIR__.'/../app/Presentation/Console/Tenancy',
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')
                ->group(base_path('routes/internal_api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(SetTraceIdMiddleware::class);
        $middleware->trustProxies(at: env('TRUSTED_PROXIES', ''));
        $middleware->redirectTo(guests: '/login', users: '/users');
        $middleware->web(prepend: [ResolveTenantMiddleware::class]);
        $middleware->web(append: [SetLocaleMiddleware::class, SetAuthContextMiddleware::class, CheckPermission::class, 'throttle:web']);
        $middleware->priority([
            ResolveTenantMiddleware::class,
            Illuminate\Auth\Middleware\Authenticate::class,
            SetAuthContextMiddleware::class,
            CheckPermission::class,
        ]);
        $middleware->api(prepend: [ResolveTenantMiddleware::class]);
        $middleware->api(append: ['throttle:api']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

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

            return redirect()->back()->withErrors(['message' => $e->userMessage($translator)]);
        });
    })->create();
