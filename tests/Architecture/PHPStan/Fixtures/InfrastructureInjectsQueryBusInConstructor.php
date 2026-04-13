<?php

declare(strict_types=1);

namespace App\Infrastructure\PhpStanFixtures;

use App\Application\Bus\QueryBus;

final class InfrastructureInjectsQueryBusInConstructor
{
    public function __construct(private QueryBus $queryBus) {}
}
