<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Team\Contract\Command\UpdateTeamCommand;
use App\Domain\Team\Contract\Command\UpdateTeamWithLabelsCommand;

/** @implements CommandHandler<UpdateTeamWithLabelsCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner UpdateTeam and SyncEntityLabels handlers emit events')]
final readonly class UpdateTeamWithLabelsHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(Command $command): void
    {
        $this->commandBus->dispatch(new UpdateTeamCommand(
            id: $command->id,
            name: $command->name,
            slug: $command->slug,
            description: $command->description,
            parentTeamId: $command->parentTeamId,
        ));

        if ($command->labelIds !== null) {
            $this->commandBus->dispatch(new SyncEntityLabelsCommand(
                entityId: $command->id,
                entityType: 'teams',
                submittedLabelIds: $command->labelIds,
                actingUserId: $command->actorId,
            ));
        }
    }
}
