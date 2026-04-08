<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Application\Bus\SkipAuditLog;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
final readonly class CommandRequiresAuditableRule implements Rule
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

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if (! $classReflection->implementsInterface(Command::class)) {
            return [];
        }

        if ($classReflection->implementsInterface(AuditableCommand::class)) {
            return [];
        }

        $nativeReflection = $classReflection->getNativeReflection();
        if ($nativeReflection->getAttributes(SkipAuditLog::class) !== []) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                sprintf(
                    'Command class %s must implement AuditableCommand or have #[SkipAuditLog] attribute.',
                    $className,
                ),
            )
                ->identifier('auditLog.missingAuditableCommand')
                ->build(),
        ];
    }
}
