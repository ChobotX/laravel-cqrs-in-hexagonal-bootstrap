<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function sprintf;
use function str_contains;
use function str_ends_with;
use function str_starts_with;

/**
 * Forbids naive DateTimeInterface::format(string-literal) patterns in Presentation HTTP code.
 *
 * @implements Rule<MethodCall>
 */
final class NoNaiveDateFormatInPresentationHttpRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'format') {
            return [];
        }

        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\Presentation\\Http\\')) {
            return [];
        }

        $firstArg = $node->args[0] ?? null;

        if (! $firstArg instanceof Arg) {
            return [];
        }

        $value = $firstArg->value;

        if (! $value instanceof String_) {
            return [];
        }

        $literal = $value->value;

        if (! $this->isLikelyNaiveDateTimeFormat($literal)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Naive datetime format literal %s in Presentation HTTP — use %s::toRfc3339Utc() or a format that includes an explicit offset (e.g. DATE_ATOM, "c").',
                $literal,
                \App\Presentation\Http\Serialization\InstantJson::class,
            ))
                ->identifier('presentation.naiveDateFormat')
                ->build(),
        ];
    }

    private function isLikelyNaiveDateTimeFormat(string $literal): bool
    {
        if (in_array($literal, ['c', 'r', 'U'], true)) {
            return false;
        }

        if (str_contains($literal, 'P') || str_contains($literal, 'O')) {
            return false;
        }

        if (str_ends_with($literal, 'Z')) {
            return false;
        }

        return str_contains($literal, 'H:i:s')
            || str_contains($literal, 'H:i')
            || str_contains($literal, 'h:i:s')
            || str_contains($literal, 'h:i');
    }
}
