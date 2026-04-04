<?php

declare(strict_types=1);

namespace App\Domain\User\Command\SetPassword;

use App\Contract\Auth\PasswordManager;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\User;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Contract\UserRepository;
use DateTimeImmutable;

/** @implements CommandHandler<SetPasswordCommand> */
final readonly class SetPasswordHandler implements CommandHandler
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

        $this->passwordManager->setPassword($user->id->value, $command->rawPassword);

        $this->eventCollector->collect(new PasswordChanged(
            userId: $user->id->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
