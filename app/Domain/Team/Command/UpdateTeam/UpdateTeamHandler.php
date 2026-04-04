<?php

declare(strict_types=1);

namespace App\Domain\Team\Command\UpdateTeam;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Team\Contract\Event\TeamUpdated;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamRepository;
use App\Domain\Team\Contract\TeamSlug;
use App\Domain\Team\Exception\TeamCycleDetectedException;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Team\TeamName;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateTeamCommand> */
final readonly class UpdateTeamHandler implements CommandHandler
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

        $teamSlug = new TeamSlug($command->slug);
        $slugOwner = $this->teamRepository->findBySlug($teamSlug);

        if ($slugOwner instanceof Team && ! $slugOwner->id->equals($teamId)) {
            throw new TeamSlugAlreadyExistsException($command->slug);
        }

        $parentTeamId = null;

        if ($command->parentTeamId !== null) {
            $parentTeamId = new TeamId($command->parentTeamId);
            $parent = $this->teamRepository->findById($parentTeamId);

            if (! $parent instanceof Team) {
                throw new TeamNotFoundException($command->parentTeamId);
            }

            if ($parentTeamId->equals($teamId) || in_array($teamId->value, $this->teamRepository->ancestorTeamIds($parentTeamId), true)) {
                throw new TeamCycleDetectedException($command->id, $command->parentTeamId);
            }
        }

        $team = new Team(
            id: $teamId,
            name: new TeamName($command->name),
            slug: $teamSlug,
            description: $command->description,
            parentTeamId: $parentTeamId,
        );

        $this->teamRepository->update($team);

        $this->eventCollector->collect(new TeamUpdated(
            teamId: $team->id->value,
            name: $team->name->value,
            slug: $team->slug->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
