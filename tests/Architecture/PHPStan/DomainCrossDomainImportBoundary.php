<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Enforces that {@see App\Domain} modules import other contexts only via those contexts’
 * dispatchable/data Contract namespaces: Command, Query, Event, Entity, ValueObject, Enum, Exception.
 */
final class DomainCrossDomainImportBoundary
{
    /**
     * Only these Contract subdirectories may cross module boundaries. Repository and Service
     * contracts must stay home-bound — cross-domain callers go via the bus instead.
     *
     * @var list<string>
     */
    private const array DISPATCHABLE_CONTRACT_SUBDIRS = [
        'Command',
        'Query',
        'Event',
        'Entity',
        'ValueObject',
        'Enum',
        'Exception',
    ];

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

        if (self::isForeignDomainDispatchableContractImport($imported, $referencedDomain)) {
            return null;
        }

        return RuleErrorBuilder::message(sprintf(
            'Domain module %s may not import %s from another domain (%s). Cross-module imports must reference App\\Domain\\%s\\Contract\\{%s} — use the bus for anything else.',
            $homeDomain,
            $imported,
            $referencedDomain,
            $referencedDomain,
            implode('|', self::DISPATCHABLE_CONTRACT_SUBDIRS),
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

    private static function isForeignDomainDispatchableContractImport(string $imported, string $referencedDomain): bool
    {
        return array_any(self::DISPATCHABLE_CONTRACT_SUBDIRS, fn ($subdir): bool => str_starts_with($imported, sprintf('App\\Domain\\%s\\Contract\\%s\\', $referencedDomain, $subdir)));
    }
}
