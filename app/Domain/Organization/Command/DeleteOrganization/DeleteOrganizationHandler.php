<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\DeleteOrganization;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\OrganizationDeleted;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationRepository;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteOrganizationCommand> */
final readonly class DeleteOrganizationHandler implements CommandHandler
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $organizationId = new OrganizationId($command->id);
        $existing = $this->organizationRepository->findById($organizationId);

        if (! $existing instanceof Organization) {
            throw new OrganizationNotFoundException($command->id);
        }

        $this->organizationRepository->delete($organizationId);

        $this->eventCollector->collect(new OrganizationDeleted(
            organizationId: $organizationId->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
