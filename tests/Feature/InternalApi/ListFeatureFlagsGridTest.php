<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\Service\FeatureFlagDefinitionProvider;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

function seedMultipleFlags(): void
{
    Config::set('feature-flags.groups', [
        'registry' => [
            'label' => 'Registry',
            'flags' => [
                'schema-builder' => [
                    'type' => 'boolean',
                    'default' => true,
                    'label' => 'Schema Builder',
                    'description' => 'Enable visual schema builder',
                ],
            ],
        ],
        'auth' => [
            'label' => 'Authentication',
            'flags' => [
                'sso-provider' => [
                    'type' => 'select',
                    'default' => 'okta',
                    'enabled' => true,
                    'label' => 'SSO Provider',
                    'description' => 'Select SSO provider',
                    'options' => ['okta', 'azure', 'google'],
                ],
            ],
        ],
        'ui' => [
            'label' => 'User Interface',
            'flags' => [
                'dark-mode' => [
                    'type' => 'boolean',
                    'default' => false,
                    'label' => 'Dark Mode',
                    'description' => 'Enable dark mode theme',
                ],
                'max-items' => [
                    'type' => 'input',
                    'default' => '25',
                    'enabled' => true,
                    'label' => 'Max Items Per Page',
                    'description' => 'Maximum items displayed per page',
                    'pattern' => '/^\d+$/',
                ],
            ],
        ],
    ]);
    app()->forgetInstance(FeatureFlagDefinitionProvider::class);
}

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

it('sorts flags by key ascending', function (): void {
    seedMultipleFlags();
    $userModel = featureFlagsGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?sort=key&direction=asc')
        ->assertOk();

    $keys = array_column($response->json('data'), 'key');
    expect($keys)->toBe(collect($keys)->sort()->values()->all())
        ->and($keys)->toHaveCount(4);
});

it('sorts flags by key descending', function (): void {
    seedMultipleFlags();
    $userModel = featureFlagsGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?sort=key&direction=desc')
        ->assertOk();

    $keys = array_column($response->json('data'), 'key');
    expect($keys)->toBe(collect($keys)->sortDesc()->values()->all());
});

it('sorts flags by type', function (): void {
    seedMultipleFlags();
    $userModel = featureFlagsGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?sort=type&direction=asc')
        ->assertOk();

    $types = array_column($response->json('data'), 'type');
    expect($types)->toBe(collect($types)->sort()->values()->all());
});

it('sorts flags by group', function (): void {
    seedMultipleFlags();
    $userModel = featureFlagsGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?sort=group&direction=asc')
        ->assertOk();

    $groups = array_column($response->json('data'), 'group_label');
    expect($groups)->toBe(collect($groups)->sort()->values()->all());
});

it('sorts flags by label descending', function (): void {
    seedMultipleFlags();
    $userModel = featureFlagsGridAdmin();

    $ascResponse = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?sort=label&direction=asc')
        ->assertOk();

    $descResponse = $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?sort=label&direction=desc')
        ->assertOk();

    $ascLabels = array_column($ascResponse->json('data'), 'label');
    $descLabels = array_column($descResponse->json('data'), 'label');
    expect($descLabels)->toBe(array_reverse($ascLabels));
});

it('ignores invalid sort column', function (): void {
    $userModel = featureFlagsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/feature-flags/list?sort=invalid_column&direction=asc')
        ->assertOk();
});
