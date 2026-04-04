<?php

declare(strict_types=1);

namespace App\Domain\Team\Command\DeleteTeam;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Team\Contract\Event\TeamDeleted;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamRepository;
use App\Domain\Team\Exception\TeamNotFoundException;
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
            occurredAt: new DateTimeImmutable,
        ));
    }
}
