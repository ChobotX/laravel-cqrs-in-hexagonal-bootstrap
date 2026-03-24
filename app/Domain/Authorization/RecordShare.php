<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

final readonly class RecordShare
{
    public function __construct(
        public string $granteeUserId,
        public string $resourceType,
        public string $resourceId,
        public Action $action,
        public string $grantorUserId,
    ) {}
}
