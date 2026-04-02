<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\Throw_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Infrastructure must not throw HTTP exceptions. Business decisions about
 * HTTP status codes belong in the Presentation layer. Infrastructure should
 * throw domain exceptions (implementing DomainException) and let Presentation
 * translate them to HTTP responses.
 *
 * @implements Rule<Throw_>
 */
final class NoHttpExceptionsInInfrastructureRule implements Rule
{
    public function getNodeType(): string
    {
        return Throw_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Infrastructure')) {
            return [];
        }

        $thrownType = $scope->getType($node->expr);
        $objectType = new ObjectType(HttpException::class);

        if (! $objectType->isSuperTypeOf($thrownType)->yes()) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Infrastructure must not throw HTTP exceptions. Throw a domain exception and let the Presentation layer translate it. Got: %s',
                $thrownType->describe(VerbosityLevel::typeOnly()),
            ))
                ->identifier('infrastructure.noHttpExceptions')
                ->build(),
        ];
    }
}
