<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\EmailTemplate\Contract\Command\SendTemplatedEmailCommand;
use App\Domain\EmailTemplate\Contract\Service\TemplatedEmailDispatcher;

/**
 * @implements CommandHandler<SendTemplatedEmailCommand>
 */
#[SkipDomainEvent(reason: 'Events are collected by TemplatedEmailDispatcher, not directly by this handler')]
final readonly class SendTemplatedEmailHandler implements CommandHandler
{
    public function __construct(
        private TemplatedEmailDispatcher $templatedEmailDispatcher,
    ) {}

    public function handle(Command $command): void
    {
        $this->templatedEmailDispatcher->dispatch(
            $command->userId,
            $command->templateType,
            $command->locale,
            $command->variables,
        );
    }
}
