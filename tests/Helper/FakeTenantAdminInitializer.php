<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Tenancy\Contract\Service\TenantAdminInitializer;
use App\Domain\User\Contract\Event\UserCreated;
use DateTimeImmutable;

final class FakeTenantAdminInitializer implements TenantAdminInitializer
{
    public ?string $initializedAdminId = null;

    public ?string $initializedAdminName = null;

    public ?string $initializedAdminEmail = null;

    public function initialize(string $adminId, string $adminName, string $adminEmail): UserCreated
    {
        $this->initializedAdminId = $adminId;
        $this->initializedAdminName = $adminName;
        $this->initializedAdminEmail = $adminEmail;

        return new UserCreated(
            userId: $adminId,
            name: $adminName,
            email: $adminEmail,
            occurredAt: new DateTimeImmutable,
        );
    }
}
