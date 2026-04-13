<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;

/**
 * Skips `Bus`, `Provider`, persistence/logging/tracing/translation/dev glue. For other `App\Infrastructure\…`
 * code, imports of `App\Domain\{Other}\…` where `Other` differs from the adapter’s inferred home module must
 * target that domain’s `Contract\` subtree only (same-domain imports may use ValueObject, Exception, etc.).
 *
 * Covers `use`, `use function`, and `use const` (including per-item types inside mixed `use` statements).
 *
 * @implements Rule<Use_>
 */
final readonly class InfrastructureCrossDomainImportsRule implements Rule
{
    public function __construct(
        private InfrastructureCrossDomainImportBoundary $infrastructureCrossDomainImportBoundary,
    ) {}

    public static function createWithDefaultBoundary(): self
    {
        return new self(InfrastructureCrossDomainImportBoundary::fromDefaultFile());
    }

    public function getNodeType(): string
    {
        return Use_::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $homeDomain = $this->infrastructureCrossDomainImportBoundary->resolveHomeDomainForInfrastructureFile($scope->getNamespace());

        if ($homeDomain === null) {
            return [];
        }

        $errors = [];

        foreach ($node->uses as $useItem) {
            if (! $this->isAnalyzableUseItemType($node->type, $useItem->type)) {
                continue;
            }

            $imported = $useItem->name->toString();

            $violation = $this->infrastructureCrossDomainImportBoundary->violationForImportedSymbol($imported, $homeDomain, $useItem->getStartLine());

            if ($violation instanceof IdentifierRuleError) {
                $errors[] = $violation;
            }
        }

        return $errors;
    }

    /**
     * @param  Use_::TYPE_*  $statementType
     * @param  Use_::TYPE_*  $useItemType
     */
    private function isAnalyzableUseItemType(int $statementType, int $useItemType): bool
    {
        $effective = $useItemType !== Use_::TYPE_UNKNOWN ? $useItemType : $statementType;

        return in_array($effective, [Use_::TYPE_NORMAL, Use_::TYPE_FUNCTION, Use_::TYPE_CONSTANT], true);
    }
}
