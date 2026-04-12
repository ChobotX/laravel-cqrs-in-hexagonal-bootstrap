<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Event\EntityUpdated;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function count;
use function explode;
use function sprintf;
use function str_ends_with;
use function str_starts_with;

/**
 * Every event class with a name ending in "Updated" in Domain/{Module}/Contract/Event/
 * must implement App\Application\Event\EntityUpdated.
 *
 * @implements Rule<Class_>
 */
final readonly class UpdatedEventMustImplementEntityUpdatedRule implements Rule
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAbstract() || $node->namespacedName === null) {
            return [];
        }

        $className = $node->namespacedName->toString();

        if (! $this->isUpdatedEventInContract($className, $scope)) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        if ($this->reflectionProvider->getClass($className)->implementsInterface(EntityUpdated::class)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                '%s in Contract/Event/ with "Updated" suffix must implement %s.',
                $className,
                EntityUpdated::class,
            ))
                ->identifier('event.updatedMustImplementEntityUpdated')
                ->build(),
        ];
    }

    private function isUpdatedEventInContract(string $className, Scope $scope): bool
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain\\')) {
            return false;
        }

        $parts = explode('\\', $namespace);

        // App\Domain\{Module}\Contract\Event = 5 parts
        if (count($parts) < 5 || $parts[3] !== 'Contract' || $parts[4] !== 'Event') {
            return false;
        }

        $shortName = $className;
        $lastBackslash = strrpos($className, '\\');

        if ($lastBackslash !== false) {
            $shortName = substr($className, $lastBackslash + 1);
        }

        return str_ends_with($shortName, 'Updated');
    }
}
