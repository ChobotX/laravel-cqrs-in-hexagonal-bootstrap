<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\AuditLog\AuditLogModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows audit log page for users with permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a00',
        'name' => 'Audit Viewer',
        'email' => 'audit-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/audit-log')
        ->assertOk()
        ->assertSee(__('messages.audit_log.title'));
});

it('returns 403 for users without audit_log.history.read permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a01',
        'name' => 'No Permission',
        'email' => 'no-audit@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/audit-log')
        ->assertForbidden();
});

it('redirects unauthenticated users to login', function (): void {
    $this->get('/audit-log')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/audit-log']));
});

it('displays audit log entries', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a02',
        'name' => 'Entry Viewer',
        'email' => 'entry-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    AuditLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440b01',
        'trace_id' => 'trace-test-123',
        'user_id' => $user->id,
        'impersonator_id' => null,
        'command' => 'App\\Domain\\User\\Command\\CreateUser\\CreateUserCommand',
        'action_label' => 'Create User',
        'entity_type' => 'user',
        'entity_id' => '550e8400-e29b-41d4-a716-446655440c01',
        'payload' => ['name' => 'John'],
        'status' => 'success',
        'ip_address' => '127.0.0.1',
        'occurred_at' => '2026-04-08 10:00:00',
    ]);

    $this->actingAs($user)
        ->get('/audit-log')
        ->assertOk()
        ->assertSee('Create User')
        ->assertSee('trace-test-123');
});

it('filters by entity type', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a03',
        'name' => 'Filter Tester',
        'email' => 'filter-tester@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    AuditLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440b02',
        'trace_id' => 'trace-filter-1',
        'user_id' => $user->id,
        'impersonator_id' => null,
        'command' => 'CreateUser',
        'action_label' => 'Create User',
        'entity_type' => 'user',
        'entity_id' => '550e8400-e29b-41d4-a716-446655440c02',
        'payload' => [],
        'status' => 'success',
        'ip_address' => null,
        'occurred_at' => '2026-04-08 10:00:00',
    ]);

    AuditLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440b03',
        'trace_id' => 'trace-filter-2',
        'user_id' => $user->id,
        'impersonator_id' => null,
        'command' => 'CreateRole',
        'action_label' => 'Create Role',
        'entity_type' => 'role',
        'entity_id' => '550e8400-e29b-41d4-a716-446655440c03',
        'payload' => [],
        'status' => 'success',
        'ip_address' => null,
        'occurred_at' => '2026-04-08 10:00:00',
    ]);

    $this->actingAs($user)
        ->get('/audit-log?entity_type=user')
        ->assertOk()
        ->assertSee('Create User')
        ->assertDontSee('Create Role');
});

it('shows trace detail page', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a04',
        'name' => 'Trace Viewer',
        'email' => 'trace-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    AuditLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440b04',
        'trace_id' => 'trace-detail-abc',
        'user_id' => $user->id,
        'impersonator_id' => null,
        'command' => 'CreateUser',
        'action_label' => 'Create User',
        'entity_type' => 'user',
        'entity_id' => '550e8400-e29b-41d4-a716-446655440c04',
        'payload' => ['name' => 'Jane'],
        'status' => 'success',
        'ip_address' => '10.0.0.1',
        'occurred_at' => '2026-04-08 10:00:00',
    ]);

    $this->actingAs($user)
        ->get('/audit-log/trace/trace-detail-abc')
        ->assertOk()
        ->assertSee('trace-detail-abc')
        ->assertSee('Create User')
        ->assertSee('10.0.0.1');
});

it('returns 403 on trace page without permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440a05',
        'name' => 'No Trace',
        'email' => 'no-trace@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/audit-log/trace/some-trace')
        ->assertForbidden();
});
