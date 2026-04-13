<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<InfrastructureCrossDomainImportsRule>
 */
final class InfrastructureCrossDomainImportsRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_non_contract_imports_from_another_domain(): void
    {
        $this->analyse([__DIR__.'/Fixtures/CrossDomainSimulatorConstantImport.php'], [
            [
                'Infrastructure may not import App\Domain\EmailTemplate\Constant\DefaultEmailTemplates from another domain (EmailTemplate). Use App\Domain\EmailTemplate\Contract\* or move composition to Domain.',
                7,
            ],
        ]);
    }

    #[Test]
    public function it_allows_cross_domain_imports_under_contract(): void
    {
        $this->analyse([__DIR__.'/Fixtures/CrossDomainSimulatorContractImport.php'], []);
    }

    #[Test]
    public function it_flags_use_function_imports_from_another_domain(): void
    {
        $this->analyse([__DIR__.'/Fixtures/CrossDomainSimulatorUseFunctionImport.php'], [
            [
                'Infrastructure may not import App\Domain\User\PhpStanFixtureInternal\stan_cross_domain_fixture_function from another domain (User). Use App\Domain\User\Contract\* or move composition to Domain.',
                7,
            ],
        ]);
    }

    #[Test]
    public function it_flags_use_const_imports_from_another_domain(): void
    {
        $this->analyse([__DIR__.'/Fixtures/CrossDomainSimulatorUseConstImport.php'], [
            [
                'Infrastructure may not import App\Domain\User\PhpStanFixtureInternal\STAN_CROSS_DOMAIN_FIXTURE_CONST from another domain (User). Use App\Domain\User\Contract\* or move composition to Domain.',
                7,
            ],
        ]);
    }

    #[Test]
    public function it_analyses_fixture_support_symbols_without_violations(): void
    {
        $this->analyse([__DIR__.'/Fixtures/UserPhpStanFixtureInternal/stan_cross_domain_symbols.php'], []);
    }

    protected function getRule(): Rule
    {
        return new InfrastructureCrossDomainImportsRule(InfrastructureCrossDomainImportBoundary::fromDefaultFile());
    }
}
