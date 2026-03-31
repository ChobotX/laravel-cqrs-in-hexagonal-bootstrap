<?php

declare(strict_types=1);

namespace App\Presentation\Http\Request\Web\Team;

enum TeamMemberAction: string
{
    case AddMember = 'add_member';
    case RemoveMember = 'remove_member';
}
