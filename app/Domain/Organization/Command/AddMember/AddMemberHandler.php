<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\AddMember;

use App\Application\Bus\QueryBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\MemberAdded;
use App\Domain\Organization\Exception\MemberAlreadyExistsException;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationMemberRepository;
use App\Domain\Organization\OrganizationRepository;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use DateTimeImmutable;

/** @implements CommandHandler<AddMemberCommand> */
final readonly class AddMemberHandler implements CommandHandler
{
    public function __construct(
        private OrganizationMemberRepository $organizationMemberRepository,
        private OrganizationRepository $organizationRepository,
        private QueryBus $queryBus,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $organization = $this->organizationRepository->findById(new OrganizationId($command->organizationId));

        if (! $organization instanceof Organization) {
            throw new OrganizationNotFoundException($command->organizationId);
        }

        $this->queryBus->dispatch(new GetUserByIdQuery($command->userId));

        if ($this->organizationMemberRepository->isMember($command->userId, $command->organizationId)) {
            throw new MemberAlreadyExistsException($command->userId, $command->organizationId);
        }

        $this->organizationMemberRepository->add($command->userId, $command->organizationId);

        $this->eventCollector->collect(new MemberAdded(
            userId: $command->userId,
            organizationId: $command->organizationId,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
