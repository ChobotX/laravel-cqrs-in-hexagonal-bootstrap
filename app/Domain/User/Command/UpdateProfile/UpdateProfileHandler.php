<?php

declare(strict_types=1);

namespace App\Domain\User\Command\UpdateProfile;

use App\Contract\Auth\PasswordManager;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\File\Contract\FileId;
use App\Domain\User\Contract\Event\UserUpdated;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Contract\UserRepository;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserName;
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

        $userName = new UserName($command->name);

        $user = new User(
            id: $existing->id,
            name: $userName,
            email: $existing->email,
            avatarFileId: $command->avatarFileId !== null ? new FileId($command->avatarFileId) : $existing->avatarFileId,
        );

        $this->userRepository->update($user);

        if ($command->rawPassword !== null && $command->rawPassword !== '') {
            $this->passwordManager->setPassword($user->id->value, $command->rawPassword);
        }

        $this->eventCollector->collect(new UserUpdated(
            userId: $user->id->value,
            name: $user->name->value,
            email: $user->email->value,
            occurredAt: new DateTimeImmutable,
            avatarFileId: $user->avatarFileId?->value,
        ));
    }
}
