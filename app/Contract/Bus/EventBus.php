<?php

declare(strict_types=1);

namespace App\Contract\Bus;

use App\Contract\Event\DomainEvent;

interface EventBus
{
    public function dispatch(DomainEvent ...$events): void;
}
