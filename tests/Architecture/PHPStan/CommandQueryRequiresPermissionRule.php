<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;
use App\Contract\Query\Query;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
final readonly class CommandQueryRequiresPermissionRule implements Rule
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

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        $type = $this->commandOrQueryType($classReflection);

        if ($type === null) {
            return [];
        }

        if ($this->hasPermissionAttribute($classReflection)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    '%s class %s must have either #[RequiresPermission] or #[SkipPermissionCheck] attribute.',
                    $type,
                    $className,
                ),
            )
                ->identifier('authorization.missingPermissionAttribute')
                ->build(),
        ];
    }

    private function commandOrQueryType(\PHPStan\Reflection\ClassReflection $classReflection): ?string
    {
        if ($classReflection->implementsInterface(Command::class)) {
            return 'Command';
        }

        if ($classReflection->implementsInterface(Query::class)) {
            return 'Query';
        }

        return null;
    }

    private function hasPermissionAttribute(\PHPStan\Reflection\ClassReflection $classReflection): bool
    {
        $nativeReflection = $classReflection->getNativeReflection();
        if ($nativeReflection->getAttributes(RequiresPermission::class) !== []) {
            return true;
        }

        return $nativeReflection->getAttributes(SkipPermissionCheck::class) !== [];
    }
}
