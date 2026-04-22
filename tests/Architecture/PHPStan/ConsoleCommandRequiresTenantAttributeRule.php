<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Attribute\TenantAgnosticCommand;
use App\Contract\Attribute\TenantAwareCommand;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_starts_with;

/**
 * @implements Rule<Class_>
 */
final readonly class ConsoleCommandRequiresTenantAttributeRule implements Rule
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

        if (! str_starts_with($className, 'App\\Presentation\\Console\\')) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if (! $classReflection->isSubclassOf(\Illuminate\Console\Command::class)) {
            return [];
        }

        $nativeReflection = $classReflection->getNativeReflection();

        if ($nativeReflection->getAttributes(TenantAwareCommand::class) !== []) {
            return [];
        }

        if ($nativeReflection->getAttributes(TenantAgnosticCommand::class) !== []) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Console command %s must have either #[TenantAwareCommand] or #[TenantAgnosticCommand] attribute.',
                    $className,
                ),
            )
                ->identifier('tenancy.consoleMissingTenantAttribute')
                ->build(),
        ];
    }
}
