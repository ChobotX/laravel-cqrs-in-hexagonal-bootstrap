<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<DomainCrossDomainImportsRule>
 */
final class DomainCrossDomainImportsRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_non_contract_imports_from_another_domain_module(): void
    {
        $this->analyse([__DIR__.'/Fixtures/Simulator/DomainCrossDomainBadImport.php'], [
            [
                'Domain module PhpStanFixtures may not import App\Domain\User\ValueObject\Email from another domain (User). Use App\Domain\User\Contract\* or communicate via bus/events.',
                7,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new DomainCrossDomainImportsRule;
    }
}
