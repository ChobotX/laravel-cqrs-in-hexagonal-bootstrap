<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<NoInfrastructureEventCollectorInjectionRule>
 */
final class NoInfrastructureEventCollectorInjectionRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_promoted_event_collector_property_in_infrastructure(): void
    {
        $this->analyse([__DIR__.'/Fixtures/InfrastructureHoldsEventCollectorProperty.php'], [
            [
                'Infrastructure class App\Infrastructure\SimulatorTenant\InfrastructureHoldsEventCollectorProperty must not declare App\Contract\Event\EventCollector as property $eventCollector. Only App\Infrastructure\Bus\InMemoryEventCollector may own the collector implementation.',
                9,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new NoInfrastructureEventCollectorInjectionRule($this->createReflectionProvider());
    }
}
