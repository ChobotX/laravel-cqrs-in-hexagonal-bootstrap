<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for update role in the Authorization bounded context; dispatched through the command bus.
 */
#[RequiresPermission('users.roles.update')]
final readonly class UpdateRoleCommand implements Command
{
    /**
     * @param  list<array{permission: string, scope: string}>  $permissions
     */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
        /** Longer human-readable explanation for admin UI or emails. */
        public string $description,
        /** Permission rows with module-defined shape (see constructor PHPDoc when present). */
        public array $permissions,
    ) {}
}
