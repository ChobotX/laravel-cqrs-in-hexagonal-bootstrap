<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Constant;

final readonly class DefaultRole
{
    public const string MANAGER_NAME = 'Manager';

    public const string MANAGER_DESCRIPTION = 'Full access within tenant';

    public const string TEAM_LEADER_NAME = 'Team Leader';

    public const string TEAM_LEADER_DESCRIPTION = 'Full access scoped to own team hierarchy';

    public const string TEAM_MEMBER_NAME = 'Team Member';

    public const string TEAM_MEMBER_DESCRIPTION = 'Can view all, create and update own resources';

    public const string EXTERNIST_NAME = 'Externist';

    public const string EXTERNIST_DESCRIPTION = 'External collaborator with own resource access';
}
