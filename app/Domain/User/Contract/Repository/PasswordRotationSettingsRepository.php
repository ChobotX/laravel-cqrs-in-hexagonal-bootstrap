<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Repository;

use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;

interface PasswordRotationSettingsRepository
{
    public function get(): PasswordRotationSettings;

    public function save(PasswordRotationSettings $passwordRotationSettings): void;
}
