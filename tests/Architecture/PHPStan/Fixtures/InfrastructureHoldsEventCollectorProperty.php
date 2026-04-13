<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use App\Contract\Event\EventCollector;

final class InfrastructureHoldsEventCollectorProperty
{
    public function __construct(private EventCollector $eventCollector) {}
}
