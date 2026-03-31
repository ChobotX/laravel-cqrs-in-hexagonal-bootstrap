<?php

declare(strict_types=1);

use App\Infrastructure\Logging\LaravelLogger;
use Illuminate\Support\Facades\Log;

it('delegates debug to Log facade', function (): void {
    Log::shouldReceive('debug')
        ->once()
        ->with('test message', ['key' => 'value']);

    $logger = new LaravelLogger;
    $logger->debug('test message', ['key' => 'value']);
});

it('delegates info to Log facade', function (): void {
    Log::shouldReceive('info')
        ->once()
        ->with('test message', []);

    $logger = new LaravelLogger;
    $logger->info('test message');
});

it('delegates warning to Log facade', function (): void {
    Log::shouldReceive('warning')
        ->once()
        ->with('test message', ['context' => 'data']);

    $logger = new LaravelLogger;
    $logger->warning('test message', ['context' => 'data']);
});

it('delegates error to Log facade', function (): void {
    Log::shouldReceive('error')
        ->once()
        ->with('test message', []);

    $logger = new LaravelLogger;
    $logger->error('test message');
});
