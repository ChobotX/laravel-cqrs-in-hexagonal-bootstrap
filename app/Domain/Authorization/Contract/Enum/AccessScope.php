<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Enum;

/**
 * Enumerates allowed values for access scope in the Authorization context.
 */
enum AccessScope: string
{
    case All = 'all';
    case TeamTree = 'team_tree';
    case Team = 'team';
    case Own = 'own';

    private const int ORDER_ALL = 4;

    private const int ORDER_TEAM_TREE = 3;

    private const int ORDER_TEAM = 2;

    private const int ORDER_OWN = 1;

    public function isMorePermissiveThan(self $other): bool
    {
        return $this->order() > $other->order();
    }

    public function order(): int
    {
        return match ($this) {
            self::All => self::ORDER_ALL,
            self::TeamTree => self::ORDER_TEAM_TREE,
            self::Team => self::ORDER_TEAM,
            self::Own => self::ORDER_OWN,
        };
    }
}
