<?php

declare(strict_types=1);

namespace App\Contract\Command;

/**
 * @template TCommand of Command
 */
interface CommandHandler
{
    /**
     * @param  TCommand  $command
     */
    public function handle(Command $command): void;
}
