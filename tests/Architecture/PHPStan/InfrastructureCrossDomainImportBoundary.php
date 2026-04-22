<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Shared “home module” inference and foreign-domain Contract check for Infrastructure PHPStan rules.
 */
final readonly class InfrastructureCrossDomainImportBoundary
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

    /**
     * @param  array<string, string>  $domainAliases  Infrastructure top-level segment => Domain module name
     */
    public function __construct(
        private array $domainAliases,
    ) {}

    /**
     * Loads {@see config/phpstan-infrastructure-domain-aliases.php} relative to the repository root.
     */
    public static function fromDefaultFile(): self
    {
        /** @var array<string, string> $aliases */
        $aliases = require dirname(__DIR__, 3).'/config/phpstan-infrastructure-domain-aliases.php';

        return new self($aliases);
    }

    public function resolveHomeDomainForInfrastructureFile(?string $namespace): ?string
    {
        if ($namespace === null || ! str_starts_with($namespace, 'App\\Infrastructure\\')) {
            return null;
        }

        return $this->infrastructureHomeDomainModule($namespace);
    }

    public function violationForImportedSymbol(string $imported, string $homeDomain, int $line): ?IdentifierRuleError
    {
        if (! str_starts_with($imported, 'App\\Domain\\')) {
            return null;
        }

        $referencedDomain = $this->extractDomainModule($imported);

        if ($referencedDomain === null || $referencedDomain === $homeDomain) {
            return null;
        }

        if ($this->isForeignDomainDispatchableContractImport($imported, $referencedDomain)) {
            return null;
        }

        return RuleErrorBuilder::message(sprintf(
            'Infrastructure may not import %s from another domain (%s). Cross-module imports must reference App\\Domain\\%s\\Contract\\{%s} — use the bus for anything else.',
            $imported,
            $referencedDomain,
            $referencedDomain,
            implode('|', self::DISPATCHABLE_CONTRACT_SUBDIRS),
        ))
            ->identifier('infrastructure.foreignDomainRequiresContract')
            ->line($line)
            ->build();
    }

    /**
     * @return list<string>
     */
    private function skipWiringBuckets(): array
    {
        return ['Bus', 'Provider', 'Persistence', 'Logging', 'Tracing', 'Translation', 'Dev'];
    }

    private function infrastructureHomeDomainModule(string $namespace): ?string
    {
        $parts = explode('\\', $namespace);

        if (count($parts) < 3) {
            return null;
        }

        if (in_array($parts[2], $this->skipWiringBuckets(), true)) {
            return null;
        }

        if ($parts[2] === 'Eloquent') {
            if (count($parts) === 3) {
                return null;
            }

            return $parts[3] ?? null;
        }

        $bucket = $parts[2];

        return $this->domainAliases[$bucket] ?? $bucket;
    }

    private function extractDomainModule(string $fqcn): ?string
    {
        $parts = explode('\\', $fqcn);

        if (count($parts) < 3 || $parts[0] !== 'App' || $parts[1] !== 'Domain') {
            return null;
        }

        return $parts[2];
    }

    private function isForeignDomainDispatchableContractImport(string $imported, string $referencedDomain): bool
    {
        return array_any(self::DISPATCHABLE_CONTRACT_SUBDIRS, fn ($subdir): bool => str_starts_with($imported, sprintf('App\\Domain\\%s\\Contract\\%s\\', $referencedDomain, $subdir)));
    }
}
