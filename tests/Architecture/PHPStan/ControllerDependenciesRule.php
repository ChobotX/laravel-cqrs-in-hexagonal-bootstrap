<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function in_array;
use function sprintf;
use function str_contains;

/**
 * Controllers may only inject bus dispatchers, auth identity, and authorization checker.
 * No domain services, repositories, infrastructure, or other dependencies.
 *
 * @implements Rule<ClassMethod>
 */
final class ControllerDependenciesRule implements Rule
{
    private const array ALLOWED_TYPES = [
        \App\Contract\Bus\CommandBus::class,
        \App\Contract\Bus\QueryBus::class,
        \App\Contract\Auth\AuthenticatedUser::class,
        \App\Contract\Auth\AuthorizationChecker::class,
        \App\Domain\Sso\Contract\Service\SsoLoginSession::class,
        \App\Contract\IdGenerator::class,
        \Illuminate\Contracts\Auth\Guard::class,
    ];

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->name->name !== '__construct' || ! str_contains($scope->getFile(), '/Presentation/Http/Controller/')) {
            return [];
        }

        $errors = [];

        foreach ($node->getParams() as $param) {
            $errors = [...$errors, ...$this->checkParam($param, $scope)];
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function checkParam(Node\Param $param, Scope $scope): array
    {
        $type = $param->type;

        if (! $type instanceof Node) {
            return [];
        }

        return $this->checkType($type, $scope);
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function checkType(Node $type, Scope $scope): array
    {
        if ($type instanceof NullableType) {
            return $this->checkType($type->type, $scope);
        }

        if ($type instanceof UnionType || $type instanceof IntersectionType) {
            $errors = [];

            foreach ($type->types as $inner) {
                $errors = [...$errors, ...$this->checkType($inner, $scope)];
            }

            return $errors;
        }

        if (! $type instanceof Name) {
            return [];
        }

        $typeName = $scope->resolveName($type);

        if (in_array($typeName, self::ALLOWED_TYPES, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Controller must not inject %s. Controllers may only depend on CommandBus, QueryBus, AuthenticatedUser, AuthorizationChecker, IdGenerator, and Guard.',
                $typeName,
            ))
                ->identifier('controller.forbiddenDependency')
                ->build(),
        ];
    }
}
