<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface InviteLinkGenerator
{
    public function generate(string $userId): string;
}
