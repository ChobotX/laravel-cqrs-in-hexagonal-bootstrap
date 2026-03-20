<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\DeleteTeam;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\TeamDeleted;
use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamRepository;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteTeamCommand> */
final readonly class DeleteTeamHandler implements CommandHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $teamId = new TeamId($command->id);
        $existing = $this->teamRepository->findById($teamId);

        if (! $existing instanceof Team) {
            throw new TeamNotFoundException($command->id);
        }

        $this->teamRepository->delete($teamId);

        $this->eventCollector->collect(new TeamDeleted(
            teamId: $teamId->value,
            organizationId: $existing->organizationId->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
