<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Team\Contract\Command\AddTeamMemberCommand;
use App\Domain\Team\Contract\Command\ManageTeamMembershipCommand;
use App\Domain\Team\Contract\Command\RemoveTeamMemberCommand;
use App\Domain\Team\Contract\Enum\TeamMembershipAction;

/** @implements CommandHandler<ManageTeamMembershipCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner AddTeamMember/RemoveTeamMember handlers emit events')]
final readonly class ManageTeamMembershipHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(Command $command): void
    {
        $inner = match ($command->action) {
            TeamMembershipAction::Add => new AddTeamMemberCommand(userId: $command->userId, teamId: $command->teamId),
            TeamMembershipAction::Remove => new RemoveTeamMemberCommand(userId: $command->userId, teamId: $command->teamId),
        };

        $this->commandBus->dispatch($inner);
    }
}
