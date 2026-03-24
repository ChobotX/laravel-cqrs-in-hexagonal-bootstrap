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
 * - RefreshDatabase is only allowed in Pest.php (centralized config)
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

    /** Allowed only in Pest.php for centralized configuration. */
    private const string REFRESH_DATABASE = \Illuminate\Foundation\Testing\RefreshDatabase::class;

    private const string TENANT_AWARE_REFRESH_DATABASE = \Tests\Helper\TenantAwareRefreshDatabase::class;

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

            if (in_array($name, self::FORBIDDEN_TRAITS, true)) {
                $errors[] = RuleErrorBuilder::message(
                    $name.' is forbidden — it breaks test isolation. '
                    .'RefreshDatabase is applied centrally via Pest.php.',
                )
                    ->identifier('test.forbiddenDatabaseTrait')
                    ->build();
            }

            if ($name === self::REFRESH_DATABASE && ! str_ends_with($file, '/Pest.php')) {
                $errors[] = RuleErrorBuilder::message(
                    'RefreshDatabase must not be imported directly — it is applied centrally in Pest.php.',
                )
                    ->identifier('test.directRefreshDatabase')
                    ->build();
            }

            if ($name === self::TENANT_AWARE_REFRESH_DATABASE && ! str_ends_with($file, '/Pest.php')) {
                $errors[] = RuleErrorBuilder::message(
                    'TenantAwareRefreshDatabase must not be imported directly — it is applied centrally in Pest.php.',
                )
                    ->identifier('test.directTenantAwareRefreshDatabase')
                    ->build();
            }
        }

        return $errors;
    }
}
