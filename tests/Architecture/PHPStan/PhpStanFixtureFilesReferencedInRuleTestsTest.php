<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class PhpStanFixtureFilesReferencedInRuleTestsTest extends TestCase
{
    #[Test]
    public function every_php_stan_fixture_file_is_passed_to_rule_test_case_analyse(): void
    {
        $fixtureRelativePaths = $this->fixturePhpRelativePaths();
        self::assertNotEmpty($fixtureRelativePaths);

        $ruleTestSource = $this->combinedPhpStanRuleTestSource();

        foreach ($fixtureRelativePaths as $fixtureRelativePath) {
            $needle = "__DIR__.'/Fixtures/".$fixtureRelativePath."'";
            self::assertStringContainsString(
                $needle,
                $ruleTestSource,
                'Fixture not referenced in PHPStan rule tests: '.$fixtureRelativePath,
            );
        }
    }

    /** @return list<string> */
    private function fixturePhpRelativePaths(): array
    {
        $fixturesDir = realpath(__DIR__.'/Fixtures');
        self::assertNotFalse($fixturesDir);

        $fixtureRelativePaths = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fixturesDir, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $absolutePath = $file->getRealPath();
                self::assertNotFalse($absolutePath);
                $fixtureRelativePaths[] = str_replace('\\', '/', substr($absolutePath, strlen($fixturesDir) + 1));
            }
        }

        return $fixtureRelativePaths;
    }

    private function combinedPhpStanRuleTestSource(): string
    {
        $paths = glob(__DIR__.'/*Test.php');
        if ($paths === false) {
            return '';
        }

        $ruleTestSource = '';
        foreach ($paths as $path) {
            $ruleTestSource .= file_get_contents($path);
        }

        return $ruleTestSource;
    }
}
