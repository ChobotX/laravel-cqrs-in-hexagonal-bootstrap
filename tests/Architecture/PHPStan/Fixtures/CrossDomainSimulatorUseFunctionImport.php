<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use function App\Domain\User\PhpStanFixtureInternal\stan_cross_domain_fixture_function;

final class CrossDomainSimulatorUseFunctionImport
{
    public function touch(): void
    {
        stan_cross_domain_fixture_function();
    }
}
