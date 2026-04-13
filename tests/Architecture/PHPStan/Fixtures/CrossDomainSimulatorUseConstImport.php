<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use const App\Domain\User\PhpStanFixtureInternal\STAN_CROSS_DOMAIN_FIXTURE_CONST;

final class CrossDomainSimulatorUseConstImport
{
    public function value(): int
    {
        return STAN_CROSS_DOMAIN_FIXTURE_CONST;
    }
}
