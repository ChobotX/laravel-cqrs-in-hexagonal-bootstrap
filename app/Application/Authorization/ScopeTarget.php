<?php

declare(strict_types=1);

namespace App\Application\Authorization;

enum ScopeTarget: string
{
    case User = 'user';
    case Team = 'team';
}
