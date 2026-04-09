<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function featureFlagsGridAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544ff00',
        'name' => 'Flags Admin',
        'email' => 'flags-admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

it('returns feature flags as json', function (): void {
    $userModel = featureFlagsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['key', 'label', 'description', 'type', 'group', 'group_label', 'enabled', 'value', 'is_overridden', 'has_options', 'edit_url', 'reset_url']],
            'meta' => ['current_page', 'per_page', 'total', 'total_pages'],
            'permissions' => ['can_update'],
        ]);
});

it('returns 401 for unauthenticated user', function (): void {
    $this->getJson('/internal-api/feature-flags/list')
        ->assertUnauthorized();
});

it('returns 403 for user without permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544ff01',
        'name' => 'Regular User',
        'email' => 'regular-flags@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->getJson('/internal-api/feature-flags/list')
        ->assertForbidden();
});

it('filters flags by search', function (): void {
    $userModel = featureFlagsGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?search=schema')
        ->assertOk();

    $keys = array_column($response->json('data'), 'key');
    expect($keys)->toContain('registry.schema-builder');
});

it('returns empty when search does not match', function (): void {
    $userModel = featureFlagsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?search=nonexistent-xyz-12345')
        ->assertOk()
        ->assertJsonPath('meta.total', 0);
});

it('filters flags by group', function (): void {
    $userModel = featureFlagsGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?filter[group]=registry')
        ->assertOk();

    $groups = array_unique(array_column($response->json('data'), 'group'));
    expect($groups)->toBe(['registry']);
});

it('returns empty when group filter does not match', function (): void {
    $userModel = featureFlagsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?filter[group]=nonexistent')
        ->assertOk()
        ->assertJsonPath('meta.total', 0);
});

it('returns correct flag structure', function (): void {
    $userModel = featureFlagsGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list')
        ->assertOk();

    /** @var list<array<string, mixed>> $data */
    $data = $response->json('data');
    /** @var array<string, mixed> $flag */
    $flag = collect($data)->firstWhere('key', 'registry.schema-builder');

    expect($flag)->not->toBeNull()
        ->and($flag['type'])->toBe('boolean')
        ->and($flag['group'])->toBe('registry')
        ->and($flag['has_options'])->toBeFalse()
        ->and($flag['label'])->toBeString()
        ->and($flag['description'])->toBeString();
});

it('returns permissions in response', function (): void {
    $userModel = featureFlagsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list')
        ->assertOk()
        ->assertJsonPath('permissions.can_update', true);
});

it('paginates results', function (): void {
    $userModel = featureFlagsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?page=1&per_page=15')
        ->assertOk()
        ->assertJsonPath('meta.current_page', 1);
});
