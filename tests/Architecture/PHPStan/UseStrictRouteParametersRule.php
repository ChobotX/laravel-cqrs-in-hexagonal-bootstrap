<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Presentation\Http\Request\FormRequest;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<MethodCall>
 */
final class UseStrictRouteParametersRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier || $node->name->name !== 'route') {
            return [];
        }

        if (! $node->var instanceof Variable || $node->var->name !== 'this') {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection || ! $classReflection->isSubclassOf(FormRequest::class)) {
            return [];
        }

        if ($classReflection->getName() === FormRequest::class) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Direct $this->route() is banned in form requests. Use typed methods (routeString, etc.) instead.')
                ->identifier('formRequest.useStrictRouteParameters')
                ->build(),
        ];
    }
}
