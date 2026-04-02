<?php

declare(strict_types=1);

namespace App\Contract\Tracing;

interface TraceContext
{
    public function traceId(): ?string;

    public function userId(): ?string;

    public function tenantId(): ?string;
}
