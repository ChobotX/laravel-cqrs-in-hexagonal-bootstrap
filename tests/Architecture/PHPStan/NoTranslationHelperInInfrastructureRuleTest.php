<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<NoTranslationHelperInInfrastructureRule>
 */
final class NoTranslationHelperInInfrastructureRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_translation_helpers_in_infrastructure(): void
    {
        $this->analyse([__DIR__.'/Fixtures/InfrastructureCallsTranslationHelper.php'], [
            [
                'Infrastructure must not call __(). Translation key selection belongs to Domain/Presentation; pass translated strings through a port.',
                11,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new NoTranslationHelperInInfrastructureRule;
    }
}
