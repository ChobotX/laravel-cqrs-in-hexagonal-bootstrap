<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows dashboard with welcome message for authenticated user', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440700',
        'name' => 'Admin User',
        'email' => 'admin-dash@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Admin User');
});

it('redirects unauthenticated user to login', function (): void {
    $this->get('/dashboard')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/dashboard']));
});

it('shows user count widget with users.list.read permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440701',
        'name' => 'Admin Count',
        'email' => 'admin-count@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440702',
        'name' => 'Other User',
        'email' => 'other@example.com',
    ]);

    $response = $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('data-testid="widget-users"')
        ->toContain('>2</p>');
});

it('shows role count widget with users.roles.read permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440703',
        'name' => 'Role Counter',
        'email' => 'role-counter@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $response = $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('data-testid="widget-roles"');
});

it('shows team count widget with teams.management.read permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440704',
        'name' => 'Team Counter',
        'email' => 'team-counter@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    TeamModel::create([
        'id' => '00000000-0000-0000-0000-000000000700',
        'name' => 'Alpha Team',
        'slug' => 'alpha-team',
        'description' => '',
    ]);

    $response = $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('data-testid="widget-teams"')
        ->toContain('>1</p>');
});

it('hides user widget when user lacks users.list.read permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Roles Only',
        'Can only see roles',
        ['users.roles.read' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440705',
        'name' => 'No Users Perm',
        'email' => 'no-users@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($user->id, $role->id);

    $response = $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk();

    $content = $response->getContent();
    expect($content)->not->toContain('data-testid="widget-users"')
        ->toContain('data-testid="widget-roles"');
});

it('shows welcome but no widgets for user with no relevant permissions', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440706',
        'name' => 'No Perms User',
        'email' => 'no-perms@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('No Perms User');

    $content = $response->getContent();
    expect($content)->not->toContain('data-testid="widget-users"')
        ->not->toContain('data-testid="widget-roles"')
        ->not->toContain('data-testid="widget-teams"');
});

it('redirects tenant root to dashboard for authenticated user', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440707',
        'name' => 'Root Redirect',
        'email' => 'root-redirect@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('http://'.testTenantDomain().'.laravel-bootstrap.local/')
        ->assertRedirect('/dashboard');
});

it('redirects authenticated user from login to dashboard', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440708',
        'name' => 'Login Redirect',
        'email' => 'login-redirect@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect('/dashboard');
});
