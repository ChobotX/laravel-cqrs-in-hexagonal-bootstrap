<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\ValueObject;

/**
 * Pre-projected row for the users grid. Optional list fields (roles/teams/labels)
 * are non-null only when the acting user has the corresponding read permission.
 */
final readonly class UserGridRow
{
    /**
     * @param  list<UserGridRoleLabel>|null  $roles
     * @param  list<UserGridTeamLabel>|null  $teams
     * @param  list<UserGridLabel>|null  $labels
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $avatarFileId,
        public string $initials,
        public ?array $roles,
        public ?array $teams,
        public ?array $labels,
        /** True when the acting user may impersonate this row's user. */
        public bool $impersonable,
    ) {}
}
