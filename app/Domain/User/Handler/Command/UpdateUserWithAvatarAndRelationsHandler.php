<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\IdGenerator;
use App\Domain\Authorization\Contract\Command\SyncUserRolesCommand;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\AvatarNamespace;
use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Team\Contract\Command\SyncUserTeamsCommand;
use App\Domain\User\Contract\Command\UpdateUserCommand;
use App\Domain\User\Contract\Command\UpdateUserWithAvatarAndRelationsCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;

/** @implements CommandHandler<UpdateUserWithAvatarAndRelationsCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner UpdateUser/Sync handlers emit events')]
final readonly class UpdateUserWithAvatarAndRelationsHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
    ) {}

    public function handle(Command $command): void
    {
        /** @var User $user */
        $user = $this->queryBus->dispatch(new GetUserByIdQuery($command->id));

        $avatarFileId = $this->resolveAvatarFileId($command, $user);
        $email = $command->email ?? $user->email->value;

        $this->commandBus->dispatch(new UpdateUserCommand(
            id: $command->id,
            name: $command->name,
            email: $email,
            avatarFileId: $avatarFileId,
        ));

        if ($command->roleIds !== null) {
            $this->commandBus->dispatch(new SyncUserRolesCommand(
                targetUserId: $command->id,
                submittedRoleIds: $command->roleIds,
                actingUserId: $command->actorId,
            ));
        }

        if ($command->teamIds !== null) {
            $this->commandBus->dispatch(new SyncUserTeamsCommand(
                userId: $command->id,
                submittedTeamIds: $command->teamIds,
                actingUserId: $command->actorId,
            ));
        }

        if ($command->labelIds !== null) {
            $this->commandBus->dispatch(new SyncEntityLabelsCommand(
                entityId: $command->id,
                entityType: 'users',
                submittedLabelIds: $command->labelIds,
                actingUserId: $command->actorId,
            ));
        }
    }

    private function resolveAvatarFileId(UpdateUserWithAvatarAndRelationsCommand $updateUserWithAvatarAndRelationsCommand, User $user): ?string
    {
        if ($updateUserWithAvatarAndRelationsCommand->removeAvatar) {
            return null;
        }

        if ($updateUserWithAvatarAndRelationsCommand->avatarUpload instanceof \App\Domain\File\Contract\ValueObject\FileUpload) {
            $fileId = $this->idGenerator->generate();

            $this->commandBus->dispatch(new StoreAvatarCommand(
                id: $fileId,
                namespace: AvatarNamespace::VALUE,
                uploadedBy: $updateUserWithAvatarAndRelationsCommand->actorId,
                upload: $updateUserWithAvatarAndRelationsCommand->avatarUpload,
            ));

            return $fileId;
        }

        return $user->avatarFileId?->value;
    }
}
