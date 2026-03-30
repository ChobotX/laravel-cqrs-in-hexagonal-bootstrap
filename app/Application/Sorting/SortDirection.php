<?php

declare(strict_types=1);

namespace App\Application\Sorting;

enum SortDirection: string
{
    case Asc = 'asc';
    case Desc = 'desc';

    public function toggle(): self
    {
        return match ($this) {
            self::Asc => self::Desc,
            self::Desc => self::Asc,
        };
    }
}
