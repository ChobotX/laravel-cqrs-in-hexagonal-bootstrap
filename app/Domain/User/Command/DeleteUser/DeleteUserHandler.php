<?php

declare(strict_types=1);

namespace App\Domain\User\Command\DeleteUser;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Event\UserDeleted;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteUserCommand> */
final readonly class DeleteUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $userId = new UserId($command->id);

        if (! $this->userRepository->findById($userId) instanceof User) {
            throw new UserNotFoundException($command->id);
        }

        $this->userRepository->delete($userId);

        $this->eventCollector->collect(new UserDeleted(
            userId: $userId->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
