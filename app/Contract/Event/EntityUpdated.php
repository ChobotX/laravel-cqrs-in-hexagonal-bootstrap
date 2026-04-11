<?php

declare(strict_types=1);

namespace App\Contract\Event;

interface EntityUpdated
{
    /** @return list<object> */
    public function changes(): array;
}
