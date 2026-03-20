<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\CreateTeam;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\TeamCreated;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationRepository;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamRepository;
use App\Domain\Organization\TeamSlug;
use DateTimeImmutable;

/** @implements CommandHandler<CreateTeamCommand> */
final readonly class CreateTeamHandler implements CommandHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
        private OrganizationRepository $organizationRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $organizationId = new OrganizationId($command->organizationId);
        $organization = $this->organizationRepository->findById($organizationId);

        if (! $organization instanceof Organization) {
            throw new OrganizationNotFoundException($command->organizationId);
        }

        $teamSlug = new TeamSlug($command->slug);
        $existing = $this->teamRepository->findBySlugInOrganization($teamSlug, $organizationId);

        if ($existing instanceof Team) {
            throw new TeamSlugAlreadyExistsException($command->slug);
        }

        $parentTeamId = null;

        if ($command->parentTeamId !== null) {
            $parentTeamId = new TeamId($command->parentTeamId);
            $parent = $this->teamRepository->findById($parentTeamId);

            if (! $parent instanceof Team || ! $parent->organizationId->equals($organizationId)) {
                throw new TeamNotFoundException($command->parentTeamId);
            }
        }

        $team = new Team(
            id: new TeamId($command->id),
            organizationId: $organizationId,
            name: new TeamName($command->name),
            slug: $teamSlug,
            description: $command->description,
            parentTeamId: $parentTeamId,
        );

        $this->teamRepository->create($team);

        $this->eventCollector->collect(new TeamCreated(
            teamId: $team->id->value,
            organizationId: $team->organizationId->value,
            name: $team->name->value,
            slug: $team->slug->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
