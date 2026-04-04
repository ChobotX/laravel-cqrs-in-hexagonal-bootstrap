<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\DeleteRole;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Event\RoleDeleted;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Exception\RoleNotFoundException;
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
