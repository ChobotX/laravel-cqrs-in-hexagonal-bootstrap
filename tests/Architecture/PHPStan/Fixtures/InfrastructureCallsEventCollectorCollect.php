<?php

declare(strict_types=1);

namespace App\Infrastructure\PhpStanFixtures;

use App\Contract\Event\EventCollector;

final class InfrastructureCallsEventCollectorCollect
{
    public function run(EventCollector $eventCollector): void
    {
        $eventCollector->collect();
    }
}
