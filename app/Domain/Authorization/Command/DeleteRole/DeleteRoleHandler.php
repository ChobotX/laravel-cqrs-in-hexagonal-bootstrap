<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\DeleteRole;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Event\RoleDeleted;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleRepository;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteRoleCommand> */
final readonly class DeleteRoleHandler implements CommandHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $roleId = new RoleId($command->id);
        $existing = $this->roleRepository->findById($roleId);

        if (! $existing instanceof Role) {
            throw new RoleNotFoundException($command->id);
        }

        $this->roleRepository->delete($roleId);

        $this->eventCollector->collect(new RoleDeleted(
            roleId: $roleId->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
