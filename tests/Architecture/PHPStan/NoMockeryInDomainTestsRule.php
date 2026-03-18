<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_contains;

/**
 * @implements Rule<StaticCall>
 */
final class NoMockeryInDomainTestsRule implements Rule
{
    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $file = $scope->getFile();

        if (! str_contains($file, '/tests/Unit/Domain/')) {
            return [];
        }

        if (! $node->class instanceof Name) {
            return [];
        }

        $className = $node->class->toString();

        if ($className !== 'Mockery' && $className !== 'Mockery\Mockery') {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Mockery is not allowed in Domain tests. Use anonymous class implementations instead.',
            )
                ->identifier('test.noMockeryInDomain')
                ->build(),
        ];
    }
}
