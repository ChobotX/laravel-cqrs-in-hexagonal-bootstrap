<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;

/**
 * @implements CommandHandler<SendTemplatedEmailCommand>
 */
final readonly class SendTemplatedEmailHandler implements CommandHandler
{
    public function __construct(
        private TemplatedEmailDispatcher $templatedEmailDispatcher,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $this->eventCollector->collect($this->templatedEmailDispatcher->dispatch(
            $command->userId,
            $command->templateType,
            $command->locale,
            $command->variables,
        ));
    }
}
