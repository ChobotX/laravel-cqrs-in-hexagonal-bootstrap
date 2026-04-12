<?php

declare(strict_types=1);

namespace App\Application\Event;

interface EntityUpdated
{
    /** @return list<PropertyChange> */
    public function changes(): array;
}
