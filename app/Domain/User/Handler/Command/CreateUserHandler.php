<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\File\Contract\FileId;
use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;
use App\Domain\User\Contract\User;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Contract\UserRepository;
use App\Domain\User\Email;
use App\Domain\User\UserName;
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
        $userName = new UserName($command->name);
        $email = new Email($command->email);

        if ($this->userRepository->findByEmail($email->value) instanceof User) {
            throw new EmailAlreadyExistsException($email->value);
        }

        $user = new User(
            id: new UserId($command->id),
            name: $userName,
            email: $email,
            avatarFileId: $command->avatarFileId !== null ? new FileId($command->avatarFileId) : null,
        );

        $this->userRepository->create($user);

        $this->eventCollector->collect(new UserCreated(
            userId: $user->id->value,
            name: $user->name->value,
            email: $user->email->value,
            occurredAt: new DateTimeImmutable,
            avatarFileId: $user->avatarFileId?->value,
        ));
    }
}
