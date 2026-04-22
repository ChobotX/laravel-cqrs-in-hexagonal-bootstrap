<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\IdGenerator;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\AvatarNamespace;
use App\Domain\Notification\Contract\Command\UpdateNotificationPreferencesCommand;
use App\Domain\User\Contract\Command\UpdateProfileCommand;
use App\Domain\User\Contract\Command\UpdateProfileWithAvatarAndPreferencesCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetOwnProfileQuery;

/** @implements CommandHandler<UpdateProfileWithAvatarAndPreferencesCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner UpdateProfile and preferences handlers emit events')]
final readonly class UpdateProfileWithAvatarAndPreferencesHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
    ) {}

    public function handle(Command $command): void
    {
        $avatarFileId = $this->resolveAvatarFileId($command);

        $this->commandBus->dispatch(new UpdateProfileCommand(
            userId: $command->userId,
            name: $command->name,
            email: $command->email,
            rawPassword: $command->rawPassword,
            avatarFileId: $avatarFileId,
        ));

        if ($command->notificationPreferences !== null) {
            $this->commandBus->dispatch(new UpdateNotificationPreferencesCommand(
                userId: $command->userId,
                preferences: $command->notificationPreferences,
            ));
        }
    }

    private function resolveAvatarFileId(UpdateProfileWithAvatarAndPreferencesCommand $updateProfileWithAvatarAndPreferencesCommand): ?string
    {
        if ($updateProfileWithAvatarAndPreferencesCommand->removeAvatar) {
            return null;
        }

        if ($updateProfileWithAvatarAndPreferencesCommand->avatarUpload instanceof \App\Domain\File\Contract\ValueObject\FileUpload) {
            $fileId = $this->idGenerator->generate();

            $this->commandBus->dispatch(new StoreAvatarCommand(
                id: $fileId,
                namespace: AvatarNamespace::VALUE,
                uploadedBy: $updateProfileWithAvatarAndPreferencesCommand->userId,
                upload: $updateProfileWithAvatarAndPreferencesCommand->avatarUpload,
            ));

            return $fileId;
        }

        /** @var User $user */
        $user = $this->queryBus->dispatch(new GetOwnProfileQuery($updateProfileWithAvatarAndPreferencesCommand->userId));

        return $user->avatarFileId?->value;
    }
}
