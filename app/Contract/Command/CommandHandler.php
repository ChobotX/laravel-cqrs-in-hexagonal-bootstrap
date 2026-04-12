<?php

declare(strict_types=1);

namespace App\Contract\Command;

/**
 * Executes one concrete command type. Domain handlers implement this interface and are registered on the command bus.
 *
 * @template TCommand of Command
 */
interface CommandHandler
{
    /**
     * Performs the command side effects and collects domain events where required by architecture rules.
     *
     * @param  TCommand  $command
     */
    public function handle(Command $command): void;
}
