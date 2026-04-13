<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use Override;
use PHPStan\Testing\RuleTestCase;

/**
 * Keeps PHPStan rule test container cache under the project (not system /tmp),
 * avoiding CI or local environments with tight /tmp quotas.
 *
 * @template TRule of \PHPStan\Rules\Rule
 *
 * @extends RuleTestCase<TRule>
 */
abstract class AbstractPHPStanRuleTestCase extends RuleTestCase
{
    #[Override]
    public static function setUpBeforeClass(): void
    {
        $tmpDir = dirname(__DIR__, 3).'/storage/framework/phpstan-rule-tests';

        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        putenv('TMPDIR='.$tmpDir);
        $_ENV['TMPDIR'] = $tmpDir;
        $_SERVER['TMPDIR'] = $tmpDir;

        parent::setUpBeforeClass();
    }
}
