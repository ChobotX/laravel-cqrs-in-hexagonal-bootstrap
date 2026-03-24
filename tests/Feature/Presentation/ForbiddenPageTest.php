<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows stop impersonation button on 403 when impersonating', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440640',
        'name' => 'Admin',
        'email' => '403-admin@test.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $viewerRole = $this->seedRoleWithPermissions(
        'Viewer',
        'Read-only',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440641',
        'name' => 'Viewer',
        'email' => '403-viewer@test.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $viewerRole->id);

    $this->actingAs($admin)->post('/impersonate/'.$viewer->id);

    $response = $this->actingAs($admin)->get('/roles/create');

    $response->assertForbidden();

    $content = $response->getContent();
    expect($content)
        ->toContain(route('impersonation.stop'))
        ->toContain(__('messages.impersonation.stop'));
});

it('does not show stop impersonation button on 403 for normal user', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440642',
        'name' => 'Viewer',
        'email' => '403-normal@test.com',
    ]);

    $response = $this->actingAs($user)->get('/roles/create');

    $response->assertForbidden();

    $content = $response->getContent();
    expect($content)
        ->not->toContain(route('impersonation.stop'))
        ->toContain(__('messages.errors.go_back'));
});
