<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends AbstractPHPStanRuleTestCase<InfrastructureCrossDomainGroupUseImportsRule>
 */
final class InfrastructureCrossDomainGroupUseImportsRuleTest extends AbstractPHPStanRuleTestCase
{
    #[Test]
    public function it_flags_non_contract_imports_inside_group_use(): void
    {
        $this->analyse([__DIR__.'/Fixtures/CrossDomainSimulatorGroupUseImport.php'], [
            [
                'Infrastructure may not import App\Domain\EmailTemplate\Constant\DefaultEmailTemplates from another domain (EmailTemplate). Use App\Domain\EmailTemplate\Contract\* or move composition to Domain.',
                7,
            ],
            [
                'Infrastructure may not import App\Domain\EmailTemplate\Constant\EmailTemplateTypes from another domain (EmailTemplate). Use App\Domain\EmailTemplate\Contract\* or move composition to Domain.',
                7,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new InfrastructureCrossDomainGroupUseImportsRule(InfrastructureCrossDomainImportBoundary::fromDefaultFile());
    }
}
