<?php

declare(strict_types=1);

namespace App\Domain\PhpStanFixtures;

use App\Contract\Event\EventCollector;

final class DomainMayCallEventCollectorCollect
{
    public function run(EventCollector $eventCollector): void
    {
        $eventCollector->collect();
    }
}
