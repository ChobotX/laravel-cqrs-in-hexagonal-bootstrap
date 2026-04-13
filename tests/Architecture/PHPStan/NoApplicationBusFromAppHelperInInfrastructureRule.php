<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Infrastructure must not resolve the application buses via the global {@see app()} helper.
 *
 * @implements Rule<\PhpParser\Node\Expr\FuncCall>
 */
final readonly class NoApplicationBusFromAppHelperInInfrastructureRule implements Rule
{
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\Infrastructure\\')) {
            return [];
        }

        if ($this->isExcludedNamespace($namespace)) {
            return [];
        }

        if (! $node->name instanceof Name || $node->name->toLowerString() !== 'app') {
            return [];
        }

        $args = $node->getArgs();

        if ($args === []) {
            return [];
        }

        if (! $this->isBusClassConstFetch($scope, $args[0]->value)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Infrastructure must not call app(CommandBus::class) or app(QueryBus::class). Inject ports in Domain handlers/services instead.',
            )
                ->identifier('infrastructure.noApplicationBusFromAppHelper')
                ->line($node->getStartLine())
                ->build(),
        ];
    }

    private function isExcludedNamespace(string $namespace): bool
    {
        return str_starts_with($namespace, 'App\\Infrastructure\\Bus\\')
            || str_starts_with($namespace, 'App\\Infrastructure\\Provider\\');
    }

    private function isBusClassConstFetch(Scope $scope, Expr $expr): bool
    {
        if (! $expr instanceof ClassConstFetch) {
            return false;
        }

        if (! $expr->name instanceof Identifier || $expr->name->toLowerString() !== 'class') {
            return false;
        }

        if (! $expr->class instanceof Name) {
            return false;
        }

        $resolved = $scope->resolveName($expr->class);

        return $resolved === CommandBus::class || $resolved === QueryBus::class;
    }
}
