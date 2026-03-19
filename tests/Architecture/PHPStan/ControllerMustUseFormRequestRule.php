<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionNamedType;

/**
 * @implements Rule<Class_>
 */
final readonly class ControllerMustUseFormRequestRule implements Rule
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

        if (! $this->hasRawRequestParameter($classReflection)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Controller %s must not type-hint Illuminate\Http\Request directly. Use a custom FormRequest subclass.',
                    $className,
                ),
            )
                ->identifier('presentation.controllerMustUseFormRequest')
                ->build(),
        ];
    }

    private function hasRawRequestParameter(\PHPStan\Reflection\ClassReflection $classReflection): bool
    {
        $nativeReflection = $classReflection->getNativeReflection();

        if (! $nativeReflection->hasMethod('__invoke')) {
            return false;
        }

        foreach ($nativeReflection->getMethod('__invoke')->getParameters() as $reflectionParameter) {
            $type = $reflectionParameter->getType();

            if ($type instanceof ReflectionNamedType && $type->getName() === \Illuminate\Http\Request::class) {
                return true;
            }
        }

        return false;
    }
}
