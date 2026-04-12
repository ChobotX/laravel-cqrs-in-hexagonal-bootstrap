<?php

declare(strict_types=1);

namespace App\Contract\Tracing;

/**
 * Correlation identifiers propagated across bus dispatch, logging, and external observability (null when unavailable).
 */
interface TraceContext
{
    /** Distributed trace or request id, if the runtime assigned one. */
    public function traceId(): ?string;

    /** Authenticated subject id, if any. */
    public function userId(): ?string;

    /** Active tenant id for multi-tenant tracing, if resolved. */
    public function tenantId(): ?string;
}
