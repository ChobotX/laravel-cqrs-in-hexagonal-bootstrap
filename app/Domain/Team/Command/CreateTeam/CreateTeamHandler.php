<?php

declare(strict_types=1);

namespace App\Domain\Team\Command\CreateTeam;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Team\Contract\Event\TeamCreated;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamRepository;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Team\Team;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;
use DateTimeImmutable;

/** @implements CommandHandler<CreateTeamCommand> */
final readonly class CreateTeamHandler implements CommandHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $teamSlug = new TeamSlug($command->slug);
        $existing = $this->teamRepository->findBySlug($teamSlug);

        if ($existing instanceof Team) {
            throw new TeamSlugAlreadyExistsException($command->slug);
        }

        $parentTeamId = null;

        if ($command->parentTeamId !== null) {
            $parentTeamId = new TeamId($command->parentTeamId);
            $parent = $this->teamRepository->findById($parentTeamId);

            if (! $parent instanceof Team) {
                throw new TeamNotFoundException($command->parentTeamId);
            }
        }

        $team = new Team(
            id: new TeamId($command->id),
            name: new TeamName($command->name),
            slug: $teamSlug,
            description: $command->description,
            parentTeamId: $parentTeamId,
        );

        $this->teamRepository->create($team);

        $this->eventCollector->collect(new TeamCreated(
            teamId: $team->id->value,
            name: $team->name->value,
            slug: $team->slug->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
