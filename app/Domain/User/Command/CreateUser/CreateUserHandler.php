<?php

declare(strict_types=1);

namespace App\Domain\User\Command\CreateUser;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Email;
use App\Domain\User\Event\UserCreated;
use App\Domain\User\Exception\EmailAlreadyExistsException;
use App\Domain\User\Exception\InvalidUserDataException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use DateTimeImmutable;

/** @implements CommandHandler<CreateUserCommand> */
final readonly class CreateUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if (trim($command->name) === '') {
            throw new InvalidUserDataException('User name must not be empty.');
        }

        $email = new Email($command->email);

        if ($this->userRepository->findByEmail($email->value) instanceof User) {
            throw new EmailAlreadyExistsException($email->value);
        }

        $user = new User(
            id: new UserId($command->id),
            name: $command->name,
            email: $email,
        );

        $this->userRepository->create($user);

        $this->eventCollector->collect(new UserCreated(
            userId: $user->id->value,
            name: $user->name,
            email: $user->email->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
