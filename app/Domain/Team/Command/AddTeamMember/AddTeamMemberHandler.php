<?php

declare(strict_types=1);

namespace App\Domain\Team\Command\AddTeamMember;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Team\Contract\Event\TeamMemberAdded;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamMemberRepository;
use App\Domain\Team\Contract\TeamRepository;
use App\Domain\Team\Exception\TeamMemberAlreadyExistsException;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Team;
use DateTimeImmutable;

/** @implements CommandHandler<AddTeamMemberCommand> */
final readonly class AddTeamMemberHandler implements CommandHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
        private TeamRepository $teamRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $team = $this->teamRepository->findById(new TeamId($command->teamId));

        if (! $team instanceof Team) {
            throw new TeamNotFoundException($command->teamId);
        }

        if ($this->teamMemberRepository->isMember($command->userId, $command->teamId)) {
            throw new TeamMemberAlreadyExistsException($command->userId, $command->teamId);
        }

        $this->teamMemberRepository->add($command->userId, $command->teamId);

        $this->eventCollector->collect(new TeamMemberAdded(
            userId: $command->userId,
            teamId: $command->teamId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
