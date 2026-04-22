<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

use function count;
use function explode;
use function in_array;
use function sprintf;
use function str_starts_with;

/**
 * App\Contract\ may only contain framework-level namespaces.
 * Domain-specific contracts must live in their owning Domain/{Module}/Contract/.
 *
 * @implements Rule<ClassLike>
 */
final class NoDomainSpecificContractsInGlobalContractRule implements Rule
{
    /** @var list<string> */
    private const array ALLOWED_SUB_NAMESPACES = [
        'Attribute',
        'Auth',
        'Bus',
        'Command',
        'Event',
        'Exception',
        'Http',
        'Logging',
        'Persistence',
        'Query',
        'Tenancy',
        'Tracing',
        'Translation',
    ];

    public function getNodeType(): string
    {
        return ClassLike::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace === null || ! str_starts_with($namespace, 'App\Contract')) {
            return [];
        }

        // App\Contract root is allowed (IdGenerator lives here)
        if ($namespace === 'App\Contract') {
            return [];
        }

        $parts = explode('\\', $namespace);

        if (count($parts) < 3) {
            return [];
        }

        $subNamespace = $parts[2];

        if (in_array($subNamespace, self::ALLOWED_SUB_NAMESPACES, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'App\Contract\%s is not a framework-level namespace. Domain-specific contracts must live in Domain/{Module}/Contract/.',
                $subNamespace,
            ))
                ->identifier('contract.noDomainSpecificContracts')
                ->build(),
        ];
    }
}
