<?php

declare(strict_types=1);

namespace App\Domain\Team\Constant;

final readonly class TeamFields
{
    public const string NAME = 'name';

    public const string SLUG = 'slug';

    public const string DESCRIPTION = 'description';

    public const string PARENT_TEAM_ID = 'parent_team_id';
}
