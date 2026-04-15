<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<NoDirectMailSendingRule>
 */
final class NoDirectMailSendingRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_direct_mail_facade_usage_outside_template_sender(): void
    {
        $this->analyse([__DIR__.'/Fixtures/InfrastructureDirectMailFacadeUsage.php'], [
            [
                'Direct Mail facade usage is forbidden. Send system emails via templated dispatcher (TemplatedEmailDispatcher).',
                13,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new NoDirectMailSendingRule;
    }
}
