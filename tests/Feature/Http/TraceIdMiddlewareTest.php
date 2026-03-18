<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\SpanContext;
use OpenTelemetry\Context\Context;

it('generates a trace id when no header is present', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440300',
        'name' => 'Test User',
        'email' => 'trace-gen@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/users');

    $response->assertHeader('X-Trace-Id');

    expect($response->headers->get('X-Trace-Id'))
        ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('inherits trace id from X-Trace-Id request header', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440301',
        'name' => 'Test User',
        'email' => 'trace-inherit@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $traceId = '550e8400-e29b-41d4-a716-446655440399';

    $response = $this->getJson('/api/users', ['X-Trace-Id' => $traceId]);

    $response->assertHeader('X-Trace-Id', $traceId);
});

it('extracts trace id from traceparent header', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440302',
        'name' => 'Test User',
        'email' => 'trace-w3c@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $traceId = '0af7651916cd43dd8448eb211c80319c';
    $traceparent = sprintf('00-%s-b7ad6b7169203331-01', $traceId);

    $response = $this->getJson('/api/users', ['traceparent' => $traceparent]);

    $response->assertHeader('X-Trace-Id', $traceId);
});

it('prefers X-Trace-Id over traceparent', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440303',
        'name' => 'Test User',
        'email' => 'trace-prefer@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $xTraceId = 'my-custom-trace-id';
    $traceparent = '00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01';

    $response = $this->getJson('/api/users', [
        'X-Trace-Id' => $xTraceId,
        'traceparent' => $traceparent,
    ]);

    $response->assertHeader('X-Trace-Id', $xTraceId);
});

it('falls back to generated uuid for malformed traceparent', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440304',
        'name' => 'Test User',
        'email' => 'trace-malformed@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/users', ['traceparent' => 'not-a-valid-traceparent']);

    $response->assertHeader('X-Trace-Id');

    expect($response->headers->get('X-Trace-Id'))
        ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('includes trace_id in domain exception json responses', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440305',
        'name' => 'Test User',
        'email' => 'trace-error@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $traceId = 'error-trace-id-123';

    $response = $this->getJson('/api/users/550e8400-e29b-41d4-a716-446655440999', [
        'X-Trace-Id' => $traceId,
    ]);

    $response->assertStatus(404)
        ->assertJson(['trace_id' => $traceId]);
});

it('uses otel trace id when active span exists', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440306',
        'name' => 'Test User',
        'email' => 'trace-otel@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $otelTraceId = '0af7651916cd43dd8448eb211c80319c';
    $spanContext = SpanContext::create($otelTraceId, 'b7ad6b7169203331');
    $span = Span::wrap($spanContext);
    $scope = Context::getCurrent()->withContextValue($span)->activate();

    try {
        $response = $this->getJson('/api/users');

        $response->assertHeader('X-Trace-Id', $otelTraceId);
    } finally {
        $scope->detach();
    }
});

it('prefers otel trace id over request headers', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440307',
        'name' => 'Test User',
        'email' => 'trace-otel-pref@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $otelTraceId = 'aabbccdd11223344aabbccdd11223344';
    $spanContext = SpanContext::create($otelTraceId, 'b7ad6b7169203331');
    $span = Span::wrap($spanContext);
    $scope = Context::getCurrent()->withContextValue($span)->activate();

    try {
        $response = $this->getJson('/api/users', [
            'X-Trace-Id' => 'should-be-ignored',
            'traceparent' => '00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01',
        ]);

        $response->assertHeader('X-Trace-Id', $otelTraceId);
    } finally {
        $scope->detach();
    }
});
