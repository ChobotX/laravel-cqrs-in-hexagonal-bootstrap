<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Event\DomainEventHandler;
use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function count;
use function explode;
use function preg_match;
use function sprintf;
use function str_starts_with;

/**
 * @implements Rule<Name>
 */
final class NoCrossDomainDependenciesRule implements Rule
{
    public function getNodeType(): string
    {
        return Name::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain\\')) {
            return [];
        }

        $currentContext = $this->extractContext($namespace);

        if ($currentContext === null) {
            return [];
        }

        $referencedName = $node->toString();

        if (! str_starts_with($referencedName, 'App\Domain\\')) {
            return [];
        }

        $referencedContext = $this->extractContext($referencedName);

        if ($referencedContext === null || $referencedContext === $currentContext) {
            return [];
        }

        // Allow DomainEventHandler implementations to import Event classes from other domains
        $classReflection = $scope->getClassReflection();

        if (
            $classReflection instanceof \PHPStan\Reflection\ClassReflection
            && $classReflection->implementsInterface(DomainEventHandler::class)
            && $this->isDomainEventReference($referencedName)
        ) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Cross-domain dependency from App\Domain\%s to App\Domain\%s is not allowed. Use events or the QueryBus for cross-context communication.',
                $currentContext,
                $referencedContext,
            ))
                ->identifier('domain.noCrossDomainDependency')
                ->build(),
        ];
    }

    private function extractContext(string $namespace): ?string
    {
        $parts = explode('\\', $namespace);

        // App\Domain\{Context}\... — context is the 3rd segment (index 2)
        if (count($parts) < 3) {
            return null;
        }

        return $parts[2];
    }

    private function isDomainEventReference(string $referencedName): bool
    {
        return preg_match('#^App\\\\Domain\\\\[^\\\\]+\\\\Event\\\\#', $referencedName) === 1;
    }
}
