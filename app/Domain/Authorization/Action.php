<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

enum Action: string
{
    case Read = 'read';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
}
