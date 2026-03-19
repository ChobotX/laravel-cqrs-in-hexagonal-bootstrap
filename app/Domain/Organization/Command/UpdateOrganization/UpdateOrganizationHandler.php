<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\UpdateOrganization;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Organization\Event\OrganizationUpdated;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Exception\OrganizationSlugAlreadyExistsException;
use App\Domain\Organization\Organization;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\OrganizationName;
use App\Domain\Organization\OrganizationRepository;
use App\Domain\Organization\OrganizationSlug;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateOrganizationCommand> */
final readonly class UpdateOrganizationHandler implements CommandHandler
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

        $organizationSlug = new OrganizationSlug($command->slug);
        $slugOwner = $this->organizationRepository->findBySlug($organizationSlug);

        if ($slugOwner instanceof Organization && ! $slugOwner->id->equals($organizationId)) {
            throw new OrganizationSlugAlreadyExistsException($command->slug);
        }

        $organization = new Organization(
            id: $organizationId,
            name: new OrganizationName($command->name),
            slug: $organizationSlug,
            description: $command->description,
        );

        $this->organizationRepository->update($organization);

        $this->eventCollector->collect(new OrganizationUpdated(
            organizationId: $organization->id->value,
            name: $organization->name->value,
            slug: $organization->slug->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
