<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Hash;

it('sets user_id and organization_id in context after authenticated request', function (): void {
    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default',
        'slug' => 'default',
        'description' => 'Default org',
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440100',
        'name' => 'Context User',
        'email' => 'contextuser@test.com',
        'password' => Hash::make('password'),
    ]);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    $this->actingAs($user)->get('/');

    expect(Context::get('user_id'))->toBe($user->id)
        ->and(Context::get('organization_id'))->toBe('00000000-0000-0000-0000-000000000001');
});

it('does not set context for unauthenticated request', function (): void {
    $this->get('/login');

    expect(Context::get('user_id'))->toBeNull();
});
