<?php

declare(strict_types=1);

namespace Tests\Helper;

use Illuminate\Contracts\Bus\Dispatcher;

final class FakeDispatcher implements Dispatcher
{
    /** @var list<object> */
    public array $dispatched = [];

    public function dispatch($command): void
    {
        assert(is_object($command));
        $this->dispatched[] = $command;
    }

    public function dispatchSync($command, $handler = null): void {}

    public function dispatchNow($command, $handler = null): void {}

    public function hasCommandHandler($command): bool
    {
        return false;
    }

    public function getCommandHandler($command): mixed
    {
        return null;
    }

    /** @param array<mixed> $pipes */
    public function pipeThrough(array $pipes): static
    {
        return $this;
    }

    /** @param array<mixed> $map */
    public function map(array $map): static
    {
        return $this;
    }
}
