<?php

declare(strict_types=1);

namespace App\Domain\User\EventHandler;

use App\Application\Bus\CommandBus;
use App\Application\Event\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\User\Contract\Command\SendUserInviteCommand;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Contract\Exception\UserNotFoundException;

/** @implements DomainEventHandler<UserCreated> */
#[RetryPolicy(tries: 3, backoff: [10, 30, 60], timeout: 30)]
final readonly class SendInviteOnUserCreated implements DomainEventHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        try {
            $this->commandBus->dispatch(new SendUserInviteCommand(
                userId: $domainEvent->userId,
            ));
        } catch (UserNotFoundException) {
            // @silent: User may already be missing when async event handler runs.
        }
    }
}
