<?php

declare(strict_types=1);

namespace Tests\Helper;

use Illuminate\Contracts\Events\Dispatcher;

final class FakeEventsDispatcher implements Dispatcher
{
    /** @var list<object> */
    public array $dispatched = [];

    public function listen($events, $listener = null): void {}

    public function hasListeners($eventName): bool
    {
        return false;
    }

    public function subscribe($subscriber): void {}

    public function until($event, $payload = []): mixed
    {
        return null;
    }

    public function dispatch($event, $payload = [], $halt = false): ?array
    {
        assert(is_object($event));
        $this->dispatched[] = $event;

        return null;
    }

    public function push($event, $payload = []): void {}

    public function flush($event): void {}

    public function forget($event): void {}

    public function forgetPushed(): void {}
}
