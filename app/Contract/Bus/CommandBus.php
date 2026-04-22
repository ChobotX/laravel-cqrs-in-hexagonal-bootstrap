<?php

declare(strict_types=1);

namespace App\Contract\Bus;

use App\Contract\Command\Command;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
