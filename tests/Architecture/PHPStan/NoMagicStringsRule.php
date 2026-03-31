<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function sprintf;
use function str_contains;

/**
 * @implements Rule<Expr>
 */
final class NoMagicStringsRule implements Rule
{
    public function getNodeType(): string
    {
        return Expr::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/app/')) {
            return [];
        }

        if ($node instanceof Identical || $node instanceof NotIdentical) {
            return $this->checkComparison($node);
        }

        if ($node instanceof Match_) {
            return $this->checkMatch($node);
        }

        return [];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkComparison(Identical|NotIdentical $node): array
    {
        $errors = [];

        foreach ([$node->left, $node->right] as $operand) {
            if ($operand instanceof String_ && $operand->value !== '') {
                $errors[] = $this->buildError($operand->value);
            }
        }

        return $errors;
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkMatch(Match_ $match): array
    {
        $conds = array_merge(...array_map(
            static fn (Node\MatchArm $matchArm): array => $matchArm->conds ?? [],
            $match->arms,
        ));

        $errors = [];

        foreach ($conds as $cond) {
            if ($cond instanceof String_ && $cond->value !== '') {
                $errors[] = $this->buildError($cond->value);
            }
        }

        return $errors;
    }

    private function buildError(string $value): \PHPStan\Rules\IdentifierRuleError
    {
        return RuleErrorBuilder::message(sprintf(
            "Use an enum or class constant instead of the string literal '%s'.",
            $value,
        ))
            ->identifier('app.noMagicStrings')
            ->build();
    }
}
