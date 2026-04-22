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
                'Domain module PhpStanFixtures may not import App\Domain\User\ValueObject\Email from another domain (User). Cross-module imports must reference App\Domain\User\Contract\{Command|Query|Event|Entity|ValueObject|Enum|Exception} — use the bus for anything else.',
                7,
            ],
        ]);
    }

    #[Test]
    public function it_flags_repository_imports_from_another_domain_module(): void
    {
        $this->analyse([__DIR__.'/Fixtures/Simulator/DomainCrossDomainRepositoryImport.php'], [
            [
                'Domain module PhpStanFixtures may not import App\Domain\User\Contract\Repository\UserRepository from another domain (User). Cross-module imports must reference App\Domain\User\Contract\{Command|Query|Event|Entity|ValueObject|Enum|Exception} — use the bus for anything else.',
                7,
            ],
        ]);
    }

    #[Test]
    public function it_flags_service_imports_from_another_domain_module(): void
    {
        $this->analyse([__DIR__.'/Fixtures/Simulator/DomainCrossDomainServiceImport.php'], [
            [
                'Domain module PhpStanFixtures may not import App\Domain\EmailTemplate\Contract\Service\EmailSender from another domain (EmailTemplate). Cross-module imports must reference App\Domain\EmailTemplate\Contract\{Command|Query|Event|Entity|ValueObject|Enum|Exception} — use the bus for anything else.',
                7,
            ],
        ]);
    }

    #[Test]
    public function it_allows_dispatchable_contract_imports_from_another_domain_module(): void
    {
        $this->analyse([__DIR__.'/Fixtures/Simulator/DomainCrossDomainGoodImport.php'], []);
    }

    protected function getRule(): Rule
    {
        return new DomainCrossDomainImportsRule;
    }
}
