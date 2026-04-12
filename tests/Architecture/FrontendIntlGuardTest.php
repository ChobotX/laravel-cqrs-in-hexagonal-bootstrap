<?php

declare(strict_types=1);

namespace Tests\Architecture;

use FilesystemIterator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function file_get_contents;
use function preg_match;
use function str_contains;

final class FrontendIntlGuardTest extends TestCase
{
    private const string ROOT = 'resources/js';

    #[Test]
    public function intl_and_locale_formatting_live_only_in_shared_datetime(): void
    {
        $violations = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                self::ROOT,
                FilesystemIterator::SKIP_DOTS,
            ),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $violations = array_merge($violations, $this->violationsForFile($file));
        }

        self::assertSame([], $violations, implode("\n", $violations));
    }

    /** @return list<string> */
    private function violationsForFile(SplFileInfo $file): array
    {
        if (! $file->isFile() || ! $this->isScannedFrontendPath($file->getPathname())) {
            return [];
        }

        return $this->intlViolationsInContents($file->getPathname(), (string) file_get_contents($file->getPathname()));
    }

    private function isScannedFrontendPath(string $path): bool
    {
        if (! str_contains($path, '.ts') && ! str_contains($path, '.vue')) {
            return false;
        }

        if (str_contains($path, '.test.ts')) {
            return false;
        }

        return ! str_contains($path, '/core/datetime/');
    }

    /** @return list<string> */
    private function intlViolationsInContents(string $path, string $contents): array
    {
        $violations = [];

        if (preg_match('/new\\s+Intl\\.DateTimeFormat\\b/', $contents) === 1) {
            $violations[] = $path.' uses Intl.DateTimeFormat; use resources/js/core/datetime/format-instant.ts instead.';
        }

        if (preg_match('/\\.toLocaleString\\s*\\(/', $contents) === 1) {
            $violations[] = $path.' uses toLocaleString(); use shared datetime helpers instead.';
        }

        if (preg_match('/\\.toLocaleDateString\\s*\\(/', $contents) === 1) {
            $violations[] = $path.' uses toLocaleDateString(); use shared datetime helpers instead.';
        }

        return $violations;
    }
}
