<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Enforces that {@see App\Domain} modules import other contexts only via those contexts’
 * `App\Domain\{Other}\Contract\…` namespaces.
 */
final class DomainCrossDomainImportBoundary
{
    public static function resolveHomeDomainForDomainFile(?string $namespace): ?string
    {
        if ($namespace === null || ! str_starts_with($namespace, 'App\\Domain\\')) {
            return null;
        }

        $parts = explode('\\', $namespace);

        if (count($parts) < 3) {
            return null;
        }

        return $parts[2];
    }

    public static function violationForImportedSymbol(string $imported, string $homeDomain, int $line): ?IdentifierRuleError
    {
        if (! str_starts_with($imported, 'App\\Domain\\')) {
            return null;
        }

        $referencedDomain = self::extractDomainModule($imported);

        if ($referencedDomain === null || $referencedDomain === $homeDomain) {
            return null;
        }

        if (self::isForeignDomainContractImport($imported, $referencedDomain)) {
            return null;
        }

        return RuleErrorBuilder::message(sprintf(
            'Domain module %s may not import %s from another domain (%s). Use App\\Domain\\%s\\Contract\\* or communicate via bus/events.',
            $homeDomain,
            $imported,
            $referencedDomain,
            $referencedDomain,
        ))
            ->identifier('domain.foreignDomainRequiresContract')
            ->line($line)
            ->build();
    }

    private static function extractDomainModule(string $fqcn): ?string
    {
        $parts = explode('\\', $fqcn);

        if (count($parts) < 3 || $parts[0] !== 'App' || $parts[1] !== 'Domain') {
            return null;
        }

        return $parts[2];
    }

    private static function isForeignDomainContractImport(string $imported, string $referencedDomain): bool
    {
        return str_starts_with($imported, sprintf('App\\Domain\\%s\\Contract\\', $referencedDomain));
    }
}
