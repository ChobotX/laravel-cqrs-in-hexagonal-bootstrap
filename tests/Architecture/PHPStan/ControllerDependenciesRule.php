<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
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
        \App\Application\Bus\CommandBus::class,
        \App\Application\Bus\QueryBus::class,
        \App\Contract\Auth\AuthenticatedUser::class,
        \App\Contract\Authorization\AuthorizationChecker::class,
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
            $error = $this->checkParam($param, $scope);

            if ($error instanceof \PHPStan\Rules\IdentifierRuleError) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    private function checkParam(Node\Param $param, Scope $scope): ?\PHPStan\Rules\IdentifierRuleError
    {
        if (! $param->type instanceof Name) {
            return null;
        }

        $typeName = $scope->resolveName($param->type);

        if (in_array($typeName, self::ALLOWED_TYPES, true)) {
            return null;
        }

        return RuleErrorBuilder::message(sprintf(
            'Controller must not inject %s. Controllers may only depend on CommandBus, QueryBus, AuthenticatedUser, AuthorizationChecker, IdGenerator, and Guard.',
            $typeName,
        ))
            ->identifier('controller.forbiddenDependency')
            ->build();
    }
}
