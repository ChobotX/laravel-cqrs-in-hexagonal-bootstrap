<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Query\GetPasswordRotationSettingsQuery;
use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;

/** @implements QueryHandler<GetPasswordRotationSettingsQuery, PasswordRotationSettings> */
final readonly class GetPasswordRotationSettingsHandler implements QueryHandler
{
    public function __construct(
        private PasswordRotationSettingsRepository $passwordRotationSettingsRepository,
    ) {}

    public function handle(Query $query): PasswordRotationSettings
    {
        return $this->passwordRotationSettingsRepository->get();
    }
}
