<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;

final class FakeCommandBus implements CommandBus
{
    /** @var list<Command> */
    public array $dispatched = [];

    public function dispatch(Command $command): void
    {
        $this->dispatched[] = $command;
    }
}
