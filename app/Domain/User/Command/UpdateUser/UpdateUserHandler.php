<?php

declare(strict_types=1);

namespace App\Domain\User\Command\UpdateUser;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Email;
use App\Domain\User\Event\UserUpdated;
use App\Domain\User\Exception\EmailAlreadyExistsException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserName;
use App\Domain\User\UserRepository;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateUserCommand> */
final readonly class UpdateUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->userRepository->findById(new UserId($command->id));

        if (! $existing instanceof User) {
            throw new UserNotFoundException($command->id);
        }

        $userName = new UserName($command->name);
        $email = new Email($command->email);

        $existingByEmail = $this->userRepository->findByEmail($email->value);

        if ($existingByEmail instanceof User && ! $existingByEmail->id->equals($existing->id)) {
            throw new EmailAlreadyExistsException($email->value);
        }

        $user = new User(
            id: $existing->id,
            name: $userName,
            email: $email,
        );

        $this->userRepository->update($user);

        $this->eventCollector->collect(new UserUpdated(
            userId: $user->id->value,
            name: $user->name->value,
            email: $user->email->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
