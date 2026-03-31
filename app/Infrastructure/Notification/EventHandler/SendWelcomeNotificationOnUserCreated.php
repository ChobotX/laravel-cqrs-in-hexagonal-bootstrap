<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\EventHandler;

use App\Application\Bus\CommandBus;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Notification\Command\SendNotification\SendNotificationCommand;
use App\Domain\User\Event\UserCreated;

/** @implements DomainEventHandler<UserCreated> */
final readonly class SendWelcomeNotificationOnUserCreated implements DomainEventHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $this->commandBus->dispatch(new SendNotificationCommand(
            recipientIds: [$domainEvent->userId],
            type: 'user.welcome',
            title: 'Welcome!',
            body: 'Welcome to the platform. You can manage your notification preferences in your profile settings.',
            level: 'info',
            link: '/profile',
        ));
    }
}
