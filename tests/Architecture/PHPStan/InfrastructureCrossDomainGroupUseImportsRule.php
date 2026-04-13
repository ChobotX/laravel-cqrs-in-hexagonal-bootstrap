<?php

declare(strict_types=1);

namespace Tests\Architecture\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;

/**
 * Same policy as {@see InfrastructureCrossDomainImportsRule} for braced group `use` statements.
 *
 * @implements Rule<GroupUse>
 */
final readonly class InfrastructureCrossDomainGroupUseImportsRule implements Rule
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
        return GroupUse::class;
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

            $combined = Name::concat($node->prefix, $useItem->name);

            if ($combined === null) {
                continue;
            }

            $imported = $combined->toString();

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
