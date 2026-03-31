<?php

declare(strict_types=1);

namespace App\Contract\Event;

interface EntityDeleted
{
    public function entityId(): string;
}
