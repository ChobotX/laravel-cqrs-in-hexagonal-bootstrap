<?php

declare(strict_types=1);

namespace App\Infrastructure\SimulatorTenant;

use Illuminate\Support\Facades\Mail;

final class InfrastructureDirectMailFacadeUsage
{
    public function run(): void
    {
        Mail::raw('body', static function (): void {});
    }
}
