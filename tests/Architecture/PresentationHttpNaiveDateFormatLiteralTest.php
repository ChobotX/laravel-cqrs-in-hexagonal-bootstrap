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
use function str_contains;

final class PresentationHttpNaiveDateFormatLiteralTest extends TestCase
{
    #[Test]
    public function presentation_http_avoids_naive_datetime_format_literal(): void
    {
        $violations = [];
        $root = dirname(__DIR__, 2).'/app/Presentation/Http';

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $root,
                FilesystemIterator::SKIP_DOTS,
            ),
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $contents = (string) file_get_contents($file->getPathname());

            if (str_contains($contents, "'Y-m-d H:i:s'") || str_contains($contents, '"Y-m-d H:i:s"')) {
                $violations[] = $file->getPathname().' contains naive Y-m-d H:i:s format literal.';
            }
        }

        self::assertSame([], $violations, implode("\n", $violations));
    }
}
