<?php

declare(strict_types=1);

namespace App\Domain\Team\Handler\Command;

use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Team\Contract\Command\SyncUserTeamsCommand;
use App\Domain\Team\Contract\Event\TeamMemberAdded;
use App\Domain\Team\Contract\Event\TeamMemberRemoved;
use App\Domain\Team\Contract\TeamMemberRepository;
use DateTimeImmutable;

use function array_diff;

/** @implements CommandHandler<SyncUserTeamsCommand> */
final readonly class SyncUserTeamsHandler implements CommandHandler
{
    private const string REQUIRED_PERMISSION = 'teams.members.update';

    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
        private AuthorizationChecker $authorizationChecker,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if ($command->submittedTeamIds === null) {
            return;
        }

        if (! $this->authorizationChecker->can($command->actingUserId, self::REQUIRED_PERMISSION)) {
            return;
        }

        $currentTeamIds = $this->teamMemberRepository->directMemberTeamIds($command->userId);

        $toAdd = array_diff($command->submittedTeamIds, $currentTeamIds);
        $toRemove = array_diff($currentTeamIds, $command->submittedTeamIds);

        foreach ($toAdd as $teamId) {
            $this->teamMemberRepository->add($command->userId, $teamId);
            $this->eventCollector->collect(new TeamMemberAdded(
                userId: $command->userId,
                teamId: $teamId,
                occurredAt: new DateTimeImmutable,
            ));
        }

        foreach ($toRemove as $teamId) {
            $this->teamMemberRepository->remove($command->userId, $teamId);
            $this->eventCollector->collect(new TeamMemberRemoved(
                userId: $command->userId,
                teamId: $teamId,
                occurredAt: new DateTimeImmutable,
            ));
        }
    }
}
