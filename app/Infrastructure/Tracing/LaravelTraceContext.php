<?php

declare(strict_types=1);

namespace App\Infrastructure\Tracing;

use App\Contract\Tracing\TraceContext;
use Illuminate\Support\Facades\Context;

final readonly class LaravelTraceContext implements TraceContext
{
    public function traceId(): ?string
    {
        $value = Context::get('trace_id');

        return is_string($value) ? $value : null;
    }

    public function userId(): ?string
    {
        $value = Context::get('user_id');

        return is_string($value) ? $value : null;
    }

    public function tenantId(): ?string
    {
        $value = Context::get('tenant_id');

        return is_string($value) ? $value : null;
    }
}
