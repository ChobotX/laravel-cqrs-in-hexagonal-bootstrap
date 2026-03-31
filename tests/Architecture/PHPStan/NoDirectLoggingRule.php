<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_contains;

/**
 * @implements Rule<Expr>
 */
final class NoDirectLoggingRule implements Rule
{
    public function getNodeType(): string
    {
        return Expr::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/app/') || str_contains($file, '/Infrastructure/Logging/')) {
            return [];
        }

        if ($node instanceof StaticCall) {
            return $this->checkStaticCall($node, $scope);
        }

        if ($node instanceof FuncCall) {
            return $this->checkFuncCall($node);
        }

        return [];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkStaticCall(StaticCall $staticCall, Scope $scope): array
    {
        if (! $staticCall->class instanceof Name) {
            return [];
        }

        $resolved = $scope->resolveName($staticCall->class);

        if ($resolved !== \Illuminate\Support\Facades\Log::class) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Direct Log:: facade usage is not allowed. Inject App\Contract\Logging\Logger instead.',
            )
                ->identifier('app.noDirectLogging')
                ->build(),
        ];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkFuncCall(FuncCall $funcCall): array
    {
        if (! $funcCall->name instanceof Name) {
            return [];
        }

        $name = $funcCall->name->toString();

        if ($name !== 'logger' && $name !== 'report') {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Direct %s() usage is not allowed. Inject App\Contract\Logging\Logger instead.',
                $name,
            ))
                ->identifier('app.noDirectLogging')
                ->build(),
        ];
    }
}
