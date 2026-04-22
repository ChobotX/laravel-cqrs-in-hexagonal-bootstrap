<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Enum;

/**
 * Discriminator for {@see \App\Domain\Team\Contract\Command\ManageTeamMembershipCommand}.
 */
enum TeamMembershipAction: string
{
    case Add = 'add_member';
    case Remove = 'remove_member';
}
