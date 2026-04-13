<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<NoInfrastructureEventCollectorCollectRule>
 */
final class NoInfrastructureEventCollectorCollectRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_event_collector_collect_inside_app_infrastructure(): void
    {
        $this->analyse([__DIR__.'/Fixtures/InfrastructureCallsEventCollectorCollect.php'], [
            [
                'Infrastructure must not call EventCollector::collect(). Raise domain events from Domain command handlers or domain services.',
                13,
            ],
        ]);
    }

    #[Test]
    public function it_allows_event_collector_collect_outside_infrastructure(): void
    {
        $this->analyse([__DIR__.'/Fixtures/DomainMayCallEventCollectorCollect.php'], []);
    }

    protected function getRule(): Rule
    {
        return new NoInfrastructureEventCollectorCollectRule;
    }
}
