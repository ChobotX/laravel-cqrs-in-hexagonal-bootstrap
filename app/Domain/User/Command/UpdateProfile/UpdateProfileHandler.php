<?php

declare(strict_types=1);

namespace App\Domain\User\Command\UpdateProfile;

use App\Contract\Auth\PasswordManager;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\User\Event\UserUpdated;
use App\Domain\User\Exception\InvalidUserDataException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateProfileCommand> */
final readonly class UpdateProfileHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordManager $passwordManager,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->userRepository->findById(new UserId($command->userId));

        if (! $existing instanceof User) {
            throw new UserNotFoundException($command->userId);
        }

        if (trim($command->name) === '') {
            throw new InvalidUserDataException('User name must not be empty.');
        }

        $user = new User(
            id: $existing->id,
            name: $command->name,
            email: $existing->email,
        );

        $this->userRepository->update($user);

        if ($command->rawPassword !== null && $command->rawPassword !== '') {
            $this->passwordManager->setPassword($user->id->value, $command->rawPassword);
        }

        $this->eventCollector->collect(new UserUpdated(
            userId: $user->id->value,
            name: $user->name,
            email: $user->email->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
