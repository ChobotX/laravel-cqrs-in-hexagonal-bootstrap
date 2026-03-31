<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Contract\Logging\Logger;
use App\Contract\Translation\Translator;
use App\Infrastructure\Logging\LaravelLogger;
use App\Infrastructure\Translation\LaravelTranslator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    private const int LOGIN_RATE_LIMIT = 5;

    private const int API_RATE_LIMIT = 60;

    private const int WEB_RATE_LIMIT = 120;

    #[Override]
    public function register(): void
    {
        $this->app->bind(Logger::class, LaravelLogger::class);
        $this->app->bind(Translator::class, LaravelTranslator::class);
    }

    public function boot(): void
    {
        $appUrl = config('app.url');

        if (is_string($appUrl) && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', static fn (Request $request): Limit => Limit::perMinute(self::LOGIN_RATE_LIMIT)->by(Str::lower((string) $request->string('email')).'|'.$request->ip()));

        RateLimiter::for('api', static fn (Request $request): Limit => Limit::perMinute(self::API_RATE_LIMIT)->by($request->user()?->getAuthIdentifier() ?? $request->ip()));

        RateLimiter::for('web', static fn (Request $request): Limit => Limit::perMinute(self::WEB_RATE_LIMIT)->by($request->user()?->getAuthIdentifier() ?? $request->ip()));
    }
}
