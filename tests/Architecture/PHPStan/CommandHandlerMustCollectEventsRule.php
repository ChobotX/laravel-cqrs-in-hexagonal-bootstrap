<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use ReflectionNamedType;

/**
 * @implements Rule<Class_>
 */
final readonly class CommandHandlerMustCollectEventsRule implements Rule
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->isAbstract() || $node->namespacedName === null) {
            return [];
        }

        $className = $node->namespacedName->toString();

        if (! $this->isCommandHandler($className)) {
            return [];
        }

        $nativeReflection = $this->reflectionProvider->getClass($className)->getNativeReflection();

        if ($nativeReflection->getAttributes(SkipDomainEvent::class) !== []) {
            return [];
        }

        if ($this->constructorInjectsEventCollector($className)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    "CommandHandler %s must inject EventCollector or declare #[SkipDomainEvent(reason: '...')].",
                    $className,
                ),
            )
                ->identifier('command.missingEventCollector')
                ->build(),
        ];
    }

    private function isCommandHandler(string $className): bool
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return false;
        }

        return $this->reflectionProvider->getClass($className)->implementsInterface(CommandHandler::class);
    }

    private function constructorInjectsEventCollector(string $className): bool
    {
        $constructor = $this->reflectionProvider->getClass($className)->getNativeReflection()->getConstructor();

        if ($constructor === null) {
            return false;
        }

        foreach ($constructor->getParameters() as $reflectionParameter) {
            $type = $reflectionParameter->getType();

            if ($type instanceof ReflectionNamedType && $type->getName() === EventCollector::class) {
                return true;
            }
        }

        return false;
    }
}
