<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Service;

use ReflectionClass;

final readonly class CommandPayloadExtractor
{
    public function deriveActionLabel(object $command): string
    {
        $className = new ReflectionClass($command)->getShortName();
        $withoutSuffix = preg_replace('/Command$/', '', $className) ?? $className;

        return trim((string) preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $withoutSuffix));
    }
}
