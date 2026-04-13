<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use App\Application\Bus\QueryBus;

final class InfrastructureCallsAppWithQueryBus
{
    public function run(): void
    {
        app(QueryBus::class);
    }
}
