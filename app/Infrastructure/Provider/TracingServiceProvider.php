<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Infrastructure\Tracing\SetConsoleTraceId;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class TracingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(CommandStarting::class, SetConsoleTraceId::class);
    }
}
