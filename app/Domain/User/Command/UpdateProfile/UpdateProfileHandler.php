<?php

declare(strict_types=1);

namespace App\Domain\User\Command\UpdateProfile;

use App\Contract\Auth\PasswordManager;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\File\Contract\FileId;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\UserUpdated;
use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\User;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Contract\UserRepository;
use App\Domain\User\Email;
use App\Domain\User\UserName;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateProfileCommand> */
final readonly class UpdateProfileHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordManager $passwordManager,
        private AuthorizationChecker $authorizationChecker,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->userRepository->findById(new UserId($command->userId));

        if (! $existing instanceof User) {
            throw new UserNotFoundException($command->userId);
        }

        $email = $this->resolveEmail($command, $existing);

        $user = new User(
            id: $existing->id,
            name: new UserName($command->name),
            email: $email,
            avatarFileId: $command->avatarFileId !== null ? new FileId($command->avatarFileId) : $existing->avatarFileId,
        );

        $this->userRepository->update($user);

        if ($command->rawPassword !== null && $command->rawPassword !== '') {
            $this->passwordManager->setPassword($user->id->value, $command->rawPassword);
            $this->eventCollector->collect(new PasswordChanged(
                userId: $user->id->value,
                occurredAt: new DateTimeImmutable,
            ));
        }

        $this->eventCollector->collect(new UserUpdated(
            userId: $user->id->value,
            name: $user->name->value,
            email: $user->email->value,
            occurredAt: new DateTimeImmutable,
            avatarFileId: $user->avatarFileId?->value,
        ));
    }

    private function resolveEmail(UpdateProfileCommand $updateProfileCommand, User $user): Email
    {
        if ($updateProfileCommand->email === null || ! $this->authorizationChecker->can($updateProfileCommand->userId, 'users.list.update')) {
            return $user->email;
        }

        $email = new Email($updateProfileCommand->email);
        $existingByEmail = $this->userRepository->findByEmail($email->value);

        if ($existingByEmail instanceof User && ! $existingByEmail->id->equals($user->id)) {
            throw new EmailAlreadyExistsException($email->value);
        }

        return $email;
    }
}
