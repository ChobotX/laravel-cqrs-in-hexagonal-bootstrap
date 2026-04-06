<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Contract\Command\ResetPasswordCommand;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\PasswordResetCompleted;
use App\Domain\User\Contract\Service\PasswordResetBroker;
use DateTimeImmutable;

/** @implements CommandHandler<ResetPasswordCommand> */
final readonly class ResetPasswordHandler implements CommandHandler
{
    public function __construct(
        private PasswordResetBroker $passwordResetBroker,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $userId = $this->passwordResetBroker->reset(
            $command->email,
            $command->token,
            $command->rawPassword,
        );

        $this->eventCollector->collect(new PasswordResetCompleted(
            userId: $userId,
            occurredAt: new DateTimeImmutable,
        ));

        $this->eventCollector->collect(new PasswordChanged(
            userId: $userId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
