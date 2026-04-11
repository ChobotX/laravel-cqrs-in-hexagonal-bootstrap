<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\File\Contract\ValueObject\FileId;
use App\Domain\User\Constant\UserFields;
use App\Domain\User\Contract\Command\UpdateProfileCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Event\PasswordChanged;
use App\Domain\User\Contract\Event\UserUpdated;
use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserName;
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
            isActivated: $existing->isActivated,
            avatarFileId: $command->avatarFileId !== null ? new FileId($command->avatarFileId) : null,
        );

        $changes = $this->buildChanges($existing, $user);

        if ($changes !== []) {
            $this->userRepository->update($user);
            $this->eventCollector->collect(new UserUpdated(
                userId: $user->id->value,
                changes: $changes,
                occurredAt: new DateTimeImmutable,
            ));
        }

        if ($command->rawPassword !== null && $command->rawPassword !== '') {
            $this->passwordManager->setPassword($user->id->value, $command->rawPassword);
            $this->eventCollector->collect(new PasswordChanged(
                userId: $user->id->value,
                occurredAt: new DateTimeImmutable,
            ));
        }
    }

    /** @return list<PropertyChange> */
    private function buildChanges(User $existing, User $updated): array
    {
        $changes = [];

        if ($existing->name->value !== $updated->name->value) {
            $changes[] = new PropertyChange(UserFields::NAME, $existing->name->value, $updated->name->value);
        }

        if ($existing->email->value !== $updated->email->value) {
            $changes[] = new PropertyChange(UserFields::EMAIL, $existing->email->value, $updated->email->value);
        }

        if ($existing->avatarFileId?->value !== $updated->avatarFileId?->value) {
            $changes[] = new PropertyChange(UserFields::AVATAR_FILE_ID, $existing->avatarFileId?->value, $updated->avatarFileId?->value);
        }

        return $changes;
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
