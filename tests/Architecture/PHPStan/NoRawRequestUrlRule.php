<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function str_starts_with;

/**
 * Bans request()->url(), request()->fullUrl(), and request()->getUri() in the
 * Presentation layer. These return the raw HTTP scheme from the underlying
 * request, which differs from the configured APP_URL when a reverse proxy
 * terminates SSL. Use url()->current(), url()->full(), or route() instead.
 *
 * @implements Rule<MethodCall>
 */
final readonly class NoRawRequestUrlRule implements Rule
{
    private const array BANNED_METHODS = ['url', 'fullUrl', 'getUri'];

    private const string REQUEST_CLASS = \Illuminate\Http\Request::class;

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Presentation')) {
            return [];
        }

        if (! $node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->name;

        if (! in_array($methodName, self::BANNED_METHODS, true)) {
            return [];
        }

        if (! $this->isRequestType($scope->getType($node->var))) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf('Calling $request->%s() in the Presentation layer is not allowed. ', $methodName)
                .'It returns the raw HTTP scheme which breaks secure cookies behind a reverse proxy. '
                .'Use url()->current(), url()->full(), or route() instead.',
            )
                ->identifier('presentation.noRawRequestUrl')
                ->build(),
        ];
    }

    private function isRequestType(\PHPStan\Type\Type $type): bool
    {
        foreach ($type->getObjectClassNames() as $className) {
            if ($className === self::REQUEST_CLASS) {
                return true;
            }

            if (! $this->reflectionProvider->hasClass($className)) {
                continue;
            }

            if ($this->reflectionProvider->getClass($className)->isSubclassOf(self::REQUEST_CLASS)) {
                return true;
            }
        }

        return false;
    }
}
