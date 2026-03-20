<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\UpdateTeam;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\TeamUpdated;
use App\Domain\Organization\Exception\TeamCycleDetectedException;
use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamRepository;
use App\Domain\Organization\TeamSlug;
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
        $slugOwner = $this->teamRepository->findBySlugInOrganization($teamSlug, $existing->organizationId);

        if ($slugOwner instanceof Team && ! $slugOwner->id->equals($teamId)) {
            throw new TeamSlugAlreadyExistsException($command->slug);
        }

        $parentTeamId = null;

        if ($command->parentTeamId !== null) {
            $parentTeamId = new TeamId($command->parentTeamId);
            $parent = $this->teamRepository->findById($parentTeamId);

            if (! $parent instanceof Team || ! $parent->organizationId->equals($existing->organizationId)) {
                throw new TeamNotFoundException($command->parentTeamId);
            }

            if ($parentTeamId->equals($teamId)) {
                throw new TeamCycleDetectedException($command->id, $command->parentTeamId);
            }
        }

        $team = new Team(
            id: $teamId,
            organizationId: $existing->organizationId,
            name: new TeamName($command->name),
            slug: $teamSlug,
            description: $command->description,
            parentTeamId: $parentTeamId,
        );

        $this->teamRepository->update($team);

        $this->eventCollector->collect(new TeamUpdated(
            teamId: $team->id->value,
            organizationId: $team->organizationId->value,
            name: $team->name->value,
            slug: $team->slug->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
