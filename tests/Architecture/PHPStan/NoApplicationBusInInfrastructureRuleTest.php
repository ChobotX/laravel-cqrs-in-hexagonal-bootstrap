<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<NoApplicationBusInInfrastructureRule>
 */
final class NoApplicationBusInInfrastructureRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_query_bus_injected_in_infrastructure_constructor(): void
    {
        $this->analyse([__DIR__.'/Fixtures/InfrastructureInjectsQueryBusInConstructor.php'], [
            [
                'Infrastructure class App\Infrastructure\PhpStanFixtures\InfrastructureInjectsQueryBusInConstructor must not depend on App\Application\Bus\QueryBus (constructor parameter $queryBus). Use Domain handlers/services and repository ports instead.',
                9,
            ],
        ]);
    }

    #[Test]
    public function it_allows_command_bus_in_infrastructure_bus_namespace(): void
    {
        $this->analyse([__DIR__.'/Fixtures/BusNamespaceMayInjectCommandBus.php'], []);
    }

    #[Test]
    public function it_allows_query_bus_in_infrastructure_provider_namespace(): void
    {
        $this->analyse([__DIR__.'/Fixtures/ProviderNamespaceMayInjectQueryBus.php'], []);
    }

    #[Test]
    public function it_flags_query_bus_typed_method_parameter_in_infrastructure(): void
    {
        $this->analyse([__DIR__.'/Fixtures/InfrastructureMethodParameterQueryBus.php'], [
            [
                'Infrastructure class App\Infrastructure\SimulatorTenant\InfrastructureMethodParameterQueryBus must not depend on App\Application\Bus\QueryBus (method handle() parameter $queryBus). Use Domain handlers/services and repository ports instead.',
                9,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new NoApplicationBusInInfrastructureRule($this->createReflectionProvider());
    }
}
