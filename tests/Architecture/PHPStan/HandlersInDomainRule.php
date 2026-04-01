<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Contract\Command\CommandHandler;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Query\QueryHandler;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function str_starts_with;

/**
 * @implements Rule<Class_>
 */
final readonly class HandlersInDomainRule implements Rule
{
    /** @var list<class-string> */
    private const array HANDLER_INTERFACES = [
        CommandHandler::class,
        QueryHandler::class,
        DomainEventHandler::class,
    ];

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAbstract()) {
            return [];
        }

        if ($node->namespacedName === null) {
            return $this->checkAnonymousClass($node, $scope);
        }

        return $this->checkNamedClass($node);
    }

    /** @return list<\PHPStan\Rules\IdentifierRuleError> */
    private function checkNamedClass(Class_ $class): array
    {
        $className = $class->namespacedName?->toString() ?? '';

        if (! str_starts_with($className, 'App\\') || str_starts_with($className, 'App\\Domain\\')) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        foreach (self::HANDLER_INTERFACES as $handlerInterface) {
            if ($classReflection->implementsInterface($handlerInterface)) {
                return [
                    RuleErrorBuilder::message(sprintf(
                        '%s implements %s but is not in App\Domain\\. All handlers must live in the Domain layer.',
                        $className,
                        $handlerInterface,
                    ))
                        ->identifier('domain.handlerLocation')
                        ->build(),
                ];
            }
        }

        return [];
    }

    /** @return list<\PHPStan\Rules\IdentifierRuleError> */
    private function checkAnonymousClass(Class_ $class, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\\') || str_starts_with($namespace, 'App\\Domain\\')) {
            return [];
        }

        foreach ($class->implements as $implement) {
            $interfaceName = $implement->toString();

            if (in_array($interfaceName, self::HANDLER_INTERFACES, true)) {
                return [
                    RuleErrorBuilder::message(sprintf(
                        'Anonymous class implements %s but is not in App\Domain\\. All handlers must live in the Domain layer.',
                        $interfaceName,
                    ))
                        ->identifier('domain.handlerLocation')
                        ->build(),
                ];
            }
        }

        return [];
    }
}
