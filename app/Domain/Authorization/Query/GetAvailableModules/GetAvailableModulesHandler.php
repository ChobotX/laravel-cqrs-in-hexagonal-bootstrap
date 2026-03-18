<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetAvailableModules;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;

/** @implements QueryHandler<GetAvailableModulesQuery, array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}>> */
final readonly class GetAvailableModulesHandler implements QueryHandler
{
    /**
     * @param  array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}>  $modules
     */
    public function __construct(
        private array $modules,
    ) {}

    /** @return array<string, array{label: string, features: array<string, array{label: string, actions: list<string>}>}> */
    public function handle(Query $query): array
    {
        return $this->modules;
    }
}
