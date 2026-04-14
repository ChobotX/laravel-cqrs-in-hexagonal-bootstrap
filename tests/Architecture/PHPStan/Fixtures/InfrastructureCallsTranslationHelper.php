<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

final class InfrastructureCallsTranslationHelper
{
    public function run(): string
    {
        return __('messages.example');
    }
}
