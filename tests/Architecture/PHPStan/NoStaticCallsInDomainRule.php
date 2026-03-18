<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<StaticCall>
 */
final readonly class NoStaticCallsInDomainRule implements Rule
{
    private const array BACKED_ENUM_METHODS = ['from', 'tryFrom', 'cases'];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Domain')) {
            return [];
        }

        if ($node->class instanceof Name && $node->class->toString() === 'parent') {
            return [];
        }

        $methodName = $node->name instanceof Node\Identifier
            ? $node->name->toString()
            : '<dynamic>';

        if ($this->isBackedEnumBuiltin($node, $scope, $methodName)) {
            return [];
        }

        $className = $node->class instanceof Name
            ? $node->class->toString()
            : '<dynamic>';

        return [
            RuleErrorBuilder::message(sprintf(
                'Static call %s::%s() is not allowed in the Domain layer. Use dependency injection instead.',
                $className,
                $methodName,
            ))
                ->identifier('domain.noStaticCall')
                ->build(),
        ];
    }

    private function isBackedEnumBuiltin(StaticCall $staticCall, Scope $scope, string $methodName): bool
    {
        if (! in_array($methodName, self::BACKED_ENUM_METHODS, true)) {
            return false;
        }

        if (! $staticCall->class instanceof Name) {
            return false;
        }

        $className = $scope->resolveName($staticCall->class);

        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        return $this->reflectionProvider->getClass($className)->isEnum();
    }
}
