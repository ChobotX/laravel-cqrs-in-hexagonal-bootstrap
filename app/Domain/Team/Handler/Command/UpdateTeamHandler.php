<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Team\Constant\TeamFields;
use App\Domain\Team\Contract\Command\UpdateTeamCommand;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Event\TeamUpdated;
use App\Domain\Team\Contract\Repository\TeamRepository;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\Exception\TeamCycleDetectedException;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Team\ValueObject\TeamName;
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

        $parentTeamId = $this->resolveParentTeamId($command, $teamId);

        $team = new Team(
            id: $teamId,
            name: new TeamName($command->name),
            slug: $teamSlug,
            description: $command->description,
            parentTeamId: $parentTeamId,
        );

        $changes = $this->buildChanges($existing, $team);

        if ($changes === []) {
            return;
        }

        $this->teamRepository->update($team);

        $this->eventCollector->collect(new TeamUpdated(
            teamId: $team->id->value,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    private function resolveParentTeamId(UpdateTeamCommand $updateTeamCommand, TeamId $teamId): ?TeamId
    {
        if ($updateTeamCommand->parentTeamId === null) {
            return null;
        }

        $parentTeamId = new TeamId($updateTeamCommand->parentTeamId);
        $parent = $this->teamRepository->findById($parentTeamId);

        if (! $parent instanceof Team) {
            throw new TeamNotFoundException($updateTeamCommand->parentTeamId);
        }

        if ($parentTeamId->equals($teamId) || in_array($teamId->value, $this->teamRepository->ancestorTeamIds($parentTeamId), true)) {
            throw new TeamCycleDetectedException($updateTeamCommand->id, $updateTeamCommand->parentTeamId);
        }

        return $parentTeamId;
    }

    /** @return list<PropertyChange> */
    private function buildChanges(Team $existing, Team $updated): array
    {
        $changes = [];

        if ($existing->name->value !== $updated->name->value) {
            $changes[] = new PropertyChange(TeamFields::NAME, $existing->name->value, $updated->name->value);
        }

        if ($existing->slug->value !== $updated->slug->value) {
            $changes[] = new PropertyChange(TeamFields::SLUG, $existing->slug->value, $updated->slug->value);
        }

        if ($existing->description !== $updated->description) {
            $changes[] = new PropertyChange(TeamFields::DESCRIPTION, $existing->description, $updated->description);
        }

        if ($existing->parentTeamId?->value !== $updated->parentTeamId?->value) {
            $changes[] = new PropertyChange(TeamFields::PARENT_TEAM_ID, $existing->parentTeamId?->value, $updated->parentTeamId?->value);
        }

        return $changes;
    }
}
