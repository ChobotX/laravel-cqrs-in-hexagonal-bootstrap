<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\AddTeamMember;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\TeamMemberAdded;
use App\Domain\Organization\Exception\TeamMemberAlreadyExistsException;
use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\Exception\UserNotOrganizationMemberException;
use App\Domain\Organization\OrganizationMemberRepository;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamMemberRepository;
use App\Domain\Organization\TeamRepository;
use DateTimeImmutable;

/** @implements CommandHandler<AddTeamMemberCommand> */
final readonly class AddTeamMemberHandler implements CommandHandler
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
        private TeamRepository $teamRepository,
        private OrganizationMemberRepository $organizationMemberRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $team = $this->teamRepository->findById(new TeamId($command->teamId));

        if (! $team instanceof Team) {
            throw new TeamNotFoundException($command->teamId);
        }

        if (! $this->organizationMemberRepository->isMember($command->userId, $team->organizationId->value)) {
            throw new UserNotOrganizationMemberException($command->userId, $team->organizationId->value);
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
