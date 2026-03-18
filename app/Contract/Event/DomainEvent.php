<?php

declare(strict_types=1);

namespace App\Contract\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function occurredAt(): DateTimeImmutable;
}
