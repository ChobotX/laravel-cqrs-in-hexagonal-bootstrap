<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use App\Infrastructure\Eloquent\HardDelete;
use App\Infrastructure\Eloquent\HasOptimisticLocking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
final readonly class EloquentModelRequiresTraitsRule implements Rule
{
    private const string MODEL_NAMESPACE = 'App\\Infrastructure\\Eloquent\\';

    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $className = $node->namespacedName?->toString() ?? '';

        if (! str_starts_with($className, self::MODEL_NAMESPACE) || ! str_ends_with($className, 'Model')) {
            return [];
        }

        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if (! $classReflection->isSubclassOf(Model::class)) {
            return [];
        }

        if ($this->isJunctionModel($node)) {
            return [];
        }

        $errors = [];

        if (! $this->usesTrait($classReflection, HasOptimisticLocking::class)) {
            $errors[] = RuleErrorBuilder::message(sprintf(
                'Entity model %s must use HasOptimisticLocking trait.',
                $className,
            ))
                ->identifier('eloquent.missingOptimisticLocking')
                ->build();
        }

        if (! $this->usesTrait($classReflection, SoftDeletes::class) && ! $this->hasHardDeleteAttribute($node)) {
            $errors[] = RuleErrorBuilder::message(sprintf(
                'Entity model %s must use SoftDeletes or declare #[HardDelete] with a reason.',
                $className,
            ))
                ->identifier('eloquent.missingSoftDeletes')
                ->build();
        }

        return $errors;
    }

    private function isJunctionModel(Class_ $class): bool
    {
        foreach ($class->getProperties() as $property) {
            $propName = $property->props[0]->name->toString();

            if ($propName === 'timestamps' && $this->isFalseConstant($property->props[0]->default)) {
                return true;
            }

            if ($propName === 'primaryKey' && $property->props[0]->default === null) {
                return true;
            }
        }

        return false;
    }

    private function isFalseConstant(?Node\Expr $expr): bool
    {
        return $expr instanceof Node\Expr\ConstFetch && $expr->name->toString() === 'false';
    }

    private function usesTrait(\PHPStan\Reflection\ClassReflection $classReflection, string $traitName): bool
    {
        return array_any($classReflection->getTraits(true), fn ($traitReflection): bool => $traitReflection->getName() === $traitName);
    }

    private function hasHardDeleteAttribute(Class_ $class): bool
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === HardDelete::class || str_ends_with($attr->name->toString(), 'HardDelete')) {
                    return true;
                }
            }
        }

        return false;
    }
}
