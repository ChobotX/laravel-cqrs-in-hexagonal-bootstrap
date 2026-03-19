<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization\EventHandler;

use App\Application\Bus\CommandBus;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesCommand;
use App\Domain\Organization\Event\OrganizationCreated;

/** @implements DomainEventHandler<OrganizationCreated> */
final readonly class SeedDefaultRolesOnOrganizationCreated implements DomainEventHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $this->commandBus->dispatch(new SeedDefaultRolesCommand(
            organizationId: $domainEvent->organizationId,
        ));
    }
}
