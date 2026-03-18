<?php

declare(strict_types=1);

namespace App\Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use OpenTelemetry\API\Trace\Span;
use Symfony\Component\HttpFoundation\Response;

final readonly class SetTraceIdMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = $this->resolveTraceId($request);

        Context::add('trace_id', $traceId);

        $response = $next($request);
        $response->headers->set('X-Trace-Id', $traceId);

        return $response;
    }

    private function resolveTraceId(Request $request): string
    {
        $spanContext = Span::getCurrent()->getContext();
        if ($spanContext->isValid()) {
            return $spanContext->getTraceId();
        }

        $xTraceId = $request->header('X-Trace-Id');

        if (is_string($xTraceId)) {
            return $xTraceId;
        }

        $traceparent = $request->header('traceparent');

        if (is_string($traceparent) && preg_match('/^[0-9a-f]{2}-([0-9a-f]{32})-[0-9a-f]{16}-[0-9a-f]{2}$/', $traceparent, $matches) === 1) {
            return $matches[1];
        }

        return Str::uuid()->toString();
    }
}
