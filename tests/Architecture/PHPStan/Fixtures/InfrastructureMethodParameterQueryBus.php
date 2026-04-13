<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use App\Application\Bus\QueryBus;

final class InfrastructureMethodParameterQueryBus
{
    public function handle(QueryBus $queryBus): void {}
}
