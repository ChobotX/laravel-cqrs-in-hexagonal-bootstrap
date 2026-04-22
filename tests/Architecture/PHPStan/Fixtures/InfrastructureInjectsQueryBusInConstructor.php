<?php

declare(strict_types=1);

namespace App\Infrastructure\PhpStanFixtures;

use App\Contract\Bus\QueryBus;

final class InfrastructureInjectsQueryBusInConstructor
{
    public function __construct(private QueryBus $queryBus) {}
}
