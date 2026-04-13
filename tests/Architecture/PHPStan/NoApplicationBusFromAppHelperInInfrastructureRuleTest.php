<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<NoApplicationBusFromAppHelperInInfrastructureRule>
 */
final class NoApplicationBusFromAppHelperInInfrastructureRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_app_helper_with_query_bus_class(): void
    {
        $this->analyse([__DIR__.'/Fixtures/InfrastructureCallsAppWithQueryBus.php'], [
            [
                'Infrastructure must not call app(CommandBus::class) or app(QueryBus::class). Inject ports in Domain handlers/services instead.',
                13,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new NoApplicationBusFromAppHelperInInfrastructureRule;
    }
}
