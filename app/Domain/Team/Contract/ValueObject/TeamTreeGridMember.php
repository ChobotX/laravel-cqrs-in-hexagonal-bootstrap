<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\ValueObject;

/**
 * Pre-projected team member entry for {@see TeamTreeGridNode}.
 */
final readonly class TeamTreeGridMember
{
    /**
     * @param  list<TeamTreeGridRoleLabel>  $roles
     */
    public function __construct(
        public string $userId,
        public string $userName,
        public ?string $avatarFileId,
        public array $roles,
    ) {}
}
