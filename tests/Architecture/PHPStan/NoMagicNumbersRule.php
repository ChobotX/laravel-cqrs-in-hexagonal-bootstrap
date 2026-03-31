<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function file;
use function preg_match;
use function sprintf;
use function str_contains;

/**
 * @implements Rule<Expr>
 */
final class NoMagicNumbersRule implements Rule
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

        if ($node instanceof UnaryMinus && $node->expr instanceof Int_ && $node->expr->value === 1) {
            return [];
        }

        if ($node instanceof Int_) {
            return $this->checkInt($node, $scope);
        }

        if ($node instanceof Float_) {
            return $this->checkFloat($node, $scope);
        }

        return [];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkInt(Int_ $int, Scope $scope): array
    {
        if ($int->value === 0 || $int->value === 1) {
            return [];
        }

        if ($this->isDefinition($int, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Use a class constant instead of the number %d.',
                $int->value,
            ))
                ->identifier('app.noMagicNumbers')
                ->build(),
        ];
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkFloat(Float_ $float, Scope $scope): array
    {
        if ($float->value === 0.0 || $float->value === 1.0) {
            return [];
        }

        if ($this->isDefinition($float, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Use a class constant instead of the number %s.',
                (string) $float->value,
            ))
                ->identifier('app.noMagicNumbers')
                ->build(),
        ];
    }

    private function isDefinition(Int_|Float_ $node, Scope $scope): bool
    {
        $line = $node->getStartLine();
        $lines = @file($scope->getFile());

        if ($lines === false || ! isset($lines[$line - 1])) {
            return false;
        }

        $content = $lines[$line - 1];

        return preg_match('/\bconst\s/', $content) === 1
            || preg_match('/\bcase\s+\w+\s*=/', $content) === 1;
    }
}
