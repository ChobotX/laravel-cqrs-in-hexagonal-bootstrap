<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\RemoveMember;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\MemberRemoved;
use App\Domain\Organization\Exception\MemberNotFoundException;
use App\Domain\Organization\OrganizationMemberRepository;
use DateTimeImmutable;

/** @implements CommandHandler<RemoveMemberCommand> */
final readonly class RemoveMemberHandler implements CommandHandler
{
    public function __construct(
        private OrganizationMemberRepository $organizationMemberRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        if (! $this->organizationMemberRepository->isMember($command->userId, $command->organizationId)) {
            throw new MemberNotFoundException($command->userId, $command->organizationId);
        }

        $this->organizationMemberRepository->remove($command->userId, $command->organizationId);

        $this->eventCollector->collect(new MemberRemoved(
            userId: $command->userId,
            organizationId: $command->organizationId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
