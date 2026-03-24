<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function str_contains;
use function str_ends_with;

/**
 * Enforces database test isolation:
 * - LazilyRefreshDatabase, DatabaseMigrations, DatabaseTransactions are forbidden everywhere
 * - RefreshDatabase and TenantAwareRefreshDatabase are only allowed in Pest.php (centralized config)
 *
 * @implements Rule<Use_>
 */
final class NoDatabaseTraitsInTestsRule implements Rule
{
    /** Traits that break transactional isolation — forbidden everywhere. */
    private const array FORBIDDEN_TRAITS = [
        \Illuminate\Foundation\Testing\LazilyRefreshDatabase::class,
        \Illuminate\Foundation\Testing\DatabaseMigrations::class,
        \Illuminate\Foundation\Testing\DatabaseTransactions::class,
    ];

    /** Traits allowed only in Pest.php for centralized configuration. */
    private const array PEST_ONLY_TRAITS = [
        \Illuminate\Foundation\Testing\RefreshDatabase::class,
        \Tests\Helper\TenantAwareRefreshDatabase::class,
    ];

    public function getNodeType(): string
    {
        return Use_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/tests/')) {
            return [];
        }

        $errors = [];

        foreach ($node->uses as $use) {
            $name = $use->name->toString();
            $error = $this->checkUse($name, $file);

            if ($error instanceof \PHPStan\Rules\RuleError) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    private function checkUse(string $name, string $file): ?\PHPStan\Rules\IdentifierRuleError
    {
        if (in_array($name, self::FORBIDDEN_TRAITS, true)) {
            return RuleErrorBuilder::message(
                $name.' is forbidden — it breaks test isolation. '
                .'Database traits are applied centrally via Pest.php.',
            )
                ->identifier('test.forbiddenDatabaseTrait')
                ->build();
        }

        if (in_array($name, self::PEST_ONLY_TRAITS, true) && ! str_ends_with($file, '/Pest.php')) {
            return RuleErrorBuilder::message(
                $name.' must not be imported directly — it is applied centrally in Pest.php.',
            )
                ->identifier('test.directDatabaseTrait')
                ->build();
        }

        return null;
    }
}
