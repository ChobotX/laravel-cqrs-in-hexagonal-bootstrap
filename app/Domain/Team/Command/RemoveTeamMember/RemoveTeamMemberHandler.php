<?php

declare(strict_types=1);

namespace App\Domain\Team\Command\RemoveTeamMember;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Team\Event\TeamMemberRemoved;
use App\Domain\Team\Exception\TeamMemberNotFoundException;
use App\Domain\Team\TeamMemberRepository;
use DateTimeImmutable;

/** @implements CommandHandler<RemoveTeamMemberCommand> */
final readonly class RemoveTeamMemberHandler implements CommandHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if (! $this->teamMemberRepository->isMember($command->userId, $command->teamId)) {
            throw new TeamMemberNotFoundException($command->userId, $command->teamId);
        }

        $this->teamMemberRepository->remove($command->userId, $command->teamId);

        $this->eventCollector->collect(new TeamMemberRemoved(
            userId: $command->userId,
            teamId: $command->teamId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
