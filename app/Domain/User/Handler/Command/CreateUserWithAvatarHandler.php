<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\IdGenerator;
use App\Domain\File\Contract\Command\StoreAvatarCommand;
use App\Domain\File\Contract\ValueObject\AvatarNamespace;
use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Domain\User\Contract\Command\CreateUserWithAvatarCommand;

/** @implements CommandHandler<CreateUserWithAvatarCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner CreateUserCommand and StoreAvatarCommand handlers emit events')]
final readonly class CreateUserWithAvatarHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
    ) {}

    public function handle(Command $command): void
    {
        $avatarFileId = null;

        if ($command->avatarUpload !== null) {
            $avatarFileId = $this->idGenerator->generate();

            $this->commandBus->dispatch(new StoreAvatarCommand(
                id: $avatarFileId,
                namespace: AvatarNamespace::VALUE,
                uploadedBy: $command->uploadedBy,
                upload: $command->avatarUpload,
            ));
        }

        $this->commandBus->dispatch(new CreateUserCommand(
            id: $command->id,
            name: $command->name,
            email: $command->email,
            avatarFileId: $avatarFileId,
        ));
    }
}
