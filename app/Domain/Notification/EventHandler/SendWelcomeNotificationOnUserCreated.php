<?php

declare(strict_types=1);

namespace App\Domain\Notification\EventHandler;

use App\Application\Bus\CommandBus;
use App\Application\Event\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Notification\Command\SendNotification\SendNotificationCommand;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\WelcomeNotification;
use App\Domain\User\Contract\Event\UserCreated;

/** @implements DomainEventHandler<UserCreated> */
#[RetryPolicy(tries: 3, backoff: [10, 30, 60], timeout: 30)]
final readonly class SendWelcomeNotificationOnUserCreated implements DomainEventHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $this->commandBus->dispatch(new SendNotificationCommand(
            recipientIds: [$domainEvent->userId],
            type: WelcomeNotification::TYPE,
            title: WelcomeNotification::TITLE,
            body: WelcomeNotification::BODY,
            level: NotificationLevel::Info->value,
            link: WelcomeNotification::LINK,
        ));
    }
}
