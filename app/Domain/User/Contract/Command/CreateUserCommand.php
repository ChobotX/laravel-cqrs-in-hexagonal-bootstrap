<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('users.list.create')]
final readonly class CreateUserCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $avatarFileId = null,
    ) {}

    public function auditEntityType(): string
    {
        return 'user';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}
