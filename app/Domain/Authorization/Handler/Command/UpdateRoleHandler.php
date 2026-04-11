<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Constant\RoleFields;
use App\Domain\Authorization\Contract\Command\UpdateRoleCommand;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Event\RoleUpdated;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Service\RolePermissionMapper;
use App\Domain\Authorization\ValueObject\RoleName;
use DateTimeImmutable;

/** @implements CommandHandler<UpdateRoleCommand> */
final readonly class UpdateRoleHandler implements CommandHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->roleRepository->findById(new RoleId($command->id));

        if (! $existing instanceof Role) {
            throw new RoleNotFoundException($command->id);
        }

        $rolePermissionMapper = new RolePermissionMapper;
        $rolePermissions = array_map($rolePermissionMapper->map(...), $command->permissions);

        $updatedRole = new Role(
            id: $existing->id,
            name: new RoleName($command->name),
            description: $command->description,
            isSystem: $existing->isSystem,
            permissions: $rolePermissions,
        );

        $changes = $this->buildChanges($existing, $updatedRole);

        if ($changes === []) {
            return;
        }

        $this->roleRepository->update($updatedRole);

        $this->eventCollector->collect(new RoleUpdated(
            roleId: $updatedRole->id->value,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    /** @return list<PropertyChange> */
    private function buildChanges(Role $existing, Role $updated): array
    {
        $changes = [];

        if ($existing->name->value !== $updated->name->value) {
            $changes[] = new PropertyChange(RoleFields::NAME, $existing->name->value, $updated->name->value);
        }

        if ($existing->description !== $updated->description) {
            $changes[] = new PropertyChange(RoleFields::DESCRIPTION, $existing->description, $updated->description);
        }

        $oldPermissions = $this->serializePermissions($existing->permissions);
        $newPermissions = $this->serializePermissions($updated->permissions);

        if ($oldPermissions !== $newPermissions) {
            $changes[] = new PropertyChange(RoleFields::PERMISSIONS, $oldPermissions, $newPermissions);
        }

        return $changes;
    }

    /** @param list<RolePermission> $permissions */
    private function serializePermissions(array $permissions): string
    {
        $entries = array_map(
            static fn (RolePermission $rolePermission): string => ($rolePermission->permissionKey).':'.$rolePermission->scope->value,
            $permissions,
        );

        sort($entries);

        return implode(',', $entries);
    }
}
