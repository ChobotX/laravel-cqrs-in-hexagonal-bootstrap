<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Authorization\RequiresPermission;
use App\Application\Authorization\SkipPermissionCheck;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
final readonly class ControllerRequiresPermissionRule implements Rule
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

        if (! str_starts_with($className, 'App\\Presentation\\Http\\Controller\\')) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if ($this->hasPermissionAttribute($classReflection)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Controller %s must have either #[RequiresPermission] or #[SkipPermissionCheck] attribute.',
                    $className,
                ),
            )
                ->identifier('authorization.controllerMissingPermissionAttribute')
                ->build(),
        ];
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
