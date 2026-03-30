<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

enum AccessScope: string
{
    case All = 'all';
    case Team = 'team';
    case Own = 'own';

    public function isMorePermissiveThan(self $other): bool
    {
        return $this->order() > $other->order();
    }

    public function order(): int
    {
        return match ($this) {
            self::All => 3,
            self::Team => 2,
            self::Own => 1,
        };
    }
}
