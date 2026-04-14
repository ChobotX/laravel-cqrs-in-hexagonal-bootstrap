<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Query\GetTwoFactorSettingsQuery;
use App\Domain\User\Contract\Repository\TwoFactorSettingsRepository;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;

/** @implements QueryHandler<GetTwoFactorSettingsQuery, TwoFactorSettings> */
final readonly class GetTwoFactorSettingsHandler implements QueryHandler
{
    public function __construct(
        private TwoFactorSettingsRepository $twoFactorSettingsRepository,
    ) {}

    public function handle(Query $query): TwoFactorSettings
    {
        return $this->twoFactorSettingsRepository->get();
    }
}
