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
 * @implements Rule<GroupUse>
 */
final readonly class DomainCrossDomainGroupUseImportsRule implements Rule
{
    public function getNodeType(): string
    {
        return GroupUse::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $homeDomain = DomainCrossDomainImportBoundary::resolveHomeDomainForDomainFile($scope->getNamespace());

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

            $violation = DomainCrossDomainImportBoundary::violationForImportedSymbol($imported, $homeDomain, $useItem->getStartLine());

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
