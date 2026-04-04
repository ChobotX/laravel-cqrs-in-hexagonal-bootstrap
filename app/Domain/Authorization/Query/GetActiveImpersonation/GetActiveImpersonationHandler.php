<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetActiveImpersonation;

use App\Contract\Authorization\ImpersonationManager;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Query\GetActiveImpersonation\GetActiveImpersonationQuery;

/** @implements QueryHandler<GetActiveImpersonationQuery, array{impersonator_id: string, impersonated_user_id: string}|null> */
final readonly class GetActiveImpersonationHandler implements QueryHandler
{
    public function __construct(
        private ImpersonationManager $impersonationManager,
    ) {}

    /** @return array{impersonator_id: string, impersonated_user_id: string}|null */
    public function handle(Query $query): ?array
    {
        if (! $this->impersonationManager->isActive()) {
            return null;
        }

        $realUserId = $this->impersonationManager->realUserId();
        $impersonatedUserId = $this->impersonationManager->impersonatedUserId();

        if ($realUserId === null || $impersonatedUserId === null) {
            return null;
        }

        return [
            'impersonator_id' => $realUserId,
            'impersonated_user_id' => $impersonatedUserId,
        ];
    }
}
