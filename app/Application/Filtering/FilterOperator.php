<?php

declare(strict_types=1);

namespace App\Application\Filtering;

enum FilterOperator: string
{
    case Equals = 'eq';
    case Contains = 'contains';
    case In = 'in';
    case Search = 'search';
}
