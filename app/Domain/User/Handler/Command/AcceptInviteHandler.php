<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Contract\Command\AcceptInviteCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\UserInviteAccepted;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<AcceptInviteCommand> */
final readonly class AcceptInviteHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordManager $passwordManager,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->userRepository->findById(new UserId($command->userId));

        if (! $user instanceof User) {
            throw new UserNotFoundException($command->userId);
        }

        if ($user->isActivated) {
            throw new UserAlreadyActivatedException($user->id->value);
        }

        $this->passwordManager->setPassword($user->id->value, $command->rawPassword);

        $this->eventCollector->collect(new UserInviteAccepted(
            userId: $user->id->value,
            occurredAt: new DateTimeImmutable,
        ));

        $this->eventCollector->collect(new PasswordChanged(
            userId: $user->id->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
