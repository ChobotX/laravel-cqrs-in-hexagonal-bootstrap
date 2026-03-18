<?php

declare(strict_types=1);

namespace App\Contract\Organization;

interface OrganizationContext
{
    public function currentOrganizationId(): ?string;
}
