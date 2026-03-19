<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\CreateOrganization;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\OrganizationCreated;
use App\Domain\Organization\Exception\OrganizationSlugAlreadyExistsException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationRepository;
use App\Domain\Organization\OrganizationSlug;
use DateTimeImmutable;

/** @implements CommandHandler<CreateOrganizationCommand> */
final readonly class CreateOrganizationHandler implements CommandHandler
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $organizationSlug = new OrganizationSlug($command->slug);
        $existing = $this->organizationRepository->findBySlug($organizationSlug);

        if ($existing instanceof Organization) {
            throw new OrganizationSlugAlreadyExistsException($command->slug);
        }

        $organization = new Organization(
            id: new OrganizationId($command->id),
            name: new OrganizationName($command->name),
            slug: $organizationSlug,
            description: $command->description,
        );

        $this->organizationRepository->create($organization);

        $this->eventCollector->collect(new OrganizationCreated(
            organizationId: $organization->id->value,
            name: $organization->name->value,
            slug: $organization->slug->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
