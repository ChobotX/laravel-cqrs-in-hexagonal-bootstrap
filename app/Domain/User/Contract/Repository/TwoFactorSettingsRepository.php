<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Repository;

use App\Domain\User\Contract\ValueObject\TwoFactorSettings;

interface TwoFactorSettingsRepository
{
    public function get(): TwoFactorSettings;

    public function save(TwoFactorSettings $twoFactorSettings): void;
}
