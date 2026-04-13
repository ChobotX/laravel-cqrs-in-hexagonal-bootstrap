<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;

/**
 * Ensures every non-wiring top-level folder under {@see app/Infrastructure} is either a Domain
 * module name or listed in {@see config/phpstan-infrastructure-domain-aliases.php}.
 */
final class InfrastructureDomainAliasMappingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var list<string>
     */
    private const array WIRING_ONLY_TOP_LEVEL = [
        'Bus',
        'Provider',
        'Persistence',
        'Logging',
        'Tracing',
        'Translation',
        'Dev',
        'Eloquent',
    ];

    #[Test]
    public function every_infrastructure_top_level_folder_is_mapped_to_an_existing_domain_module(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $infraRoot = $projectRoot.'/app/Infrastructure';
        $domainRoot = $projectRoot.'/app/Domain';

        /** @var array<string, string> $aliases */
        $aliases = require $projectRoot.'/config/phpstan-infrastructure-domain-aliases.php';

        $domainModules = $this->listImmediateSubdirectoryNames($domainRoot);
        $infraTopLevel = $this->listImmediateSubdirectoryNames($infraRoot);

        foreach ($aliases as $from => $to) {
            Assert::assertContains(
                $to,
                $domainModules,
                sprintf(
                    'Alias "%s" => "%s" must target an existing App\\Domain module directory.',
                    $from,
                    $to,
                ),
            );
        }

        foreach ($infraTopLevel as $segment) {
            if (in_array($segment, self::WIRING_ONLY_TOP_LEVEL, true)) {
                continue;
            }

            if (in_array($segment, $domainModules, true)) {
                continue;
            }

            Assert::assertArrayHasKey(
                $segment,
                $aliases,
                sprintf(
                    'Add "%s" to config/phpstan-infrastructure-domain-aliases.php (or rename app/Infrastructure/%s to match App\\Domain\\{Module}).',
                    $segment,
                    $segment,
                ),
            );

            $target = $aliases[$segment];
            Assert::assertContains(
                $target,
                $domainModules,
                sprintf(
                    'Alias "%s" => "%s" must target an existing App\\Domain module directory.',
                    $segment,
                    $target,
                ),
            );
        }
    }

    /**
     * @return list<string>
     */
    private function listImmediateSubdirectoryNames(string $path): array
    {
        if (! is_dir($path)) {
            return [];
        }

        $names = [];

        $entries = scandir($path);

        if ($entries === false) {
            return [];
        }

        foreach ($entries as $entry) {
            if ($entry === '.') {
                continue;
            }

            if ($entry === '..') {
                continue;
            }

            if (is_dir($path.'/'.$entry)) {
                $names[] = $entry;
            }
        }

        sort($names);

        return $names;
    }
}
