<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Authorization\Contract\Command\CreateSystemRoleCommand;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RoleName;

/** @implements CommandHandler<CreateSystemRoleCommand> */
#[SkipDomainEvent(reason: 'Bootstrap-only system role creation during tenant initialization')]
final readonly class CreateSystemRoleHandler implements CommandHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ) {}

    public function handle(Command $command): void
    {
        $this->roleRepository->create(new Role(
            id: new RoleId($command->id),
            name: new RoleName($command->name),
            description: $command->description,
            isSystem: true,
            permissions: [],
        ));
    }
}
