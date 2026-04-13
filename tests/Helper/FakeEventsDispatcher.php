<?php

declare(strict_types=1);

namespace Tests\Helper;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;

final class FakeEventsDispatcher implements Dispatcher
{
    /** @var list<object> */
    public array $dispatched = [];

    /**
     * @param  Closure|string|array<int|string, mixed>  $events
     * @param  Closure|string|array<int|string, mixed>|null  $listener
     */
    public function listen($events, $listener = null): void {}

    /**
     * @param  string  $eventName
     */
    public function hasListeners($eventName): bool
    {
        return false;
    }

    /**
     * @param  object|string  $subscriber
     */
    public function subscribe($subscriber): void {}

    /**
     * @param  string|object  $event
     */
    public function until($event, $payload = []): mixed
    {
        return null;
    }

    /**
     * @param  string|object  $event
     * @return array<int, mixed>|null
     */
    public function dispatch($event, $payload = [], $halt = false): ?array
    {
        assert(is_object($event));
        $this->dispatched[] = $event;

        return null;
    }

    /**
     * @param  string  $event
     * @param  array<int|string, mixed>  $payload
     */
    public function push($event, $payload = []): void {}

    /**
     * @param  string  $event
     */
    public function flush($event): void {}

    /**
     * @param  string  $event
     */
    public function forget($event): void {}

    public function forgetPushed(): void {}
}
