<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;

/**
 * UUID / ULID generation must go through App\Contract\IdGenerator. The only legal call-site for
 * `Illuminate\Support\Str::uuid()` / `Str::ulid()` / `Str::uuid7()` is `App\Infrastructure\UuidIdGenerator`,
 * the port implementation.
 *
 * @implements Rule<StaticCall>
 */
final class NoStrUuidOutsideIdGeneratorRule implements Rule
{
    private const string ALLOWED_CLASS = \App\Infrastructure\UuidIdGenerator::class;

    /** @var list<string> */
    private const array FORBIDDEN_METHODS = ['uuid', 'uuid7', 'ulid'];

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Node\Name || ! $node->name instanceof Node\Identifier) {
            return [];
        }

        $className = $scope->resolveName($node->class);

        if ($className !== \Illuminate\Support\Str::class) {
            return [];
        }

        $methodName = $node->name->toLowerString();

        if (! in_array($methodName, self::FORBIDDEN_METHODS, true)) {
            return [];
        }

        if ($scope->isInClass() && $scope->getClassReflection()->getName() === self::ALLOWED_CLASS) {
            return [];
        }

        $file = $scope->getFile();

        if (str_contains($file, '/tests/')) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Str::%s() must not be called outside %s. Inject App\Contract\IdGenerator and call generate().',
                $methodName,
                self::ALLOWED_CLASS,
            ))
                ->identifier('ids.noStrUuidOutsideGenerator')
                ->build(),
        ];
    }
}
