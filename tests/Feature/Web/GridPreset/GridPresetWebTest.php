<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\GridPreset\GridPresetModel;
use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function presetUser(): UserModel
{
    return UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f00',
        'name' => 'Preset User',
        'email' => 'preset-user@example.com',
        'password' => Hash::make('password123'),
    ]);
}

function otherPresetUser(): UserModel
{
    return UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f01',
        'name' => 'Other User',
        'email' => 'other-preset@example.com',
        'password' => Hash::make('password123'),
    ]);
}

// --- SaveGridPresetController ---

it('saves a new grid preset', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'My Preset',
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
            'search' => '',
            'is_default' => false,
        ])
        ->assertCreated();

    $this->assertDatabaseHas('grid_presets', [
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'My Preset',
    ]);
});

it('saves a preset with explicit preset_id for update', function (): void {
    $userModel = presetUser();
    $presetId = '550e8400-e29b-41d4-a716-446655440f10';

    GridPresetModel::create([
        'id' => $presetId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'Old Name',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'preset_id' => $presetId,
            'name' => 'Updated Name',
            'grid_name' => 'users',
            'filters' => '{"status":"active"}',
            'sorting' => '{"column":"name","direction":"asc"}',
            'search' => 'test',
            'is_default' => true,
        ])
        ->assertCreated();

    $this->assertDatabaseHas('grid_presets', [
        'id' => $presetId,
        'name' => 'Updated Name',
        'filters' => '{"status":"active"}',
        'sorting' => '{"column":"name","direction":"asc"}',
        'search' => 'test',
        'is_default' => true,
    ]);
});

it('validates required fields when saving preset', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'grid_name', 'filters', 'sorting']);
});

it('validates name max length when saving preset', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => str_repeat('x', 101),
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('validates grid_name max length when saving preset', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'Valid',
            'grid_name' => str_repeat('x', 101),
            'filters' => '[]',
            'sorting' => '[]',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['grid_name']);
});

it('validates preset_id must be uuid', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'Test',
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
            'preset_id' => 'not-a-uuid',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['preset_id']);
});

it('validates search max length when saving preset', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'Test',
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
            'search' => str_repeat('x', 201),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['search']);
});

it('validates scope must be valid value', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'Test',
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
            'scope' => 'invalid',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['scope']);
});

it('validates team_id required when scope is team', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'Test',
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
            'scope' => 'team',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['team_id']);
});

it('validates team_id must be uuid', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'Test',
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
            'scope' => 'team',
            'team_id' => 'not-a-uuid',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['team_id']);
});

it('saves preset with default personal scope', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets', [
            'name' => 'Default Scope',
            'grid_name' => 'users',
            'filters' => '[]',
            'sorting' => '[]',
        ])
        ->assertCreated();

    $this->assertDatabaseHas('grid_presets', [
        'user_id' => $userModel->id,
        'name' => 'Default Scope',
        'scope' => 'personal',
        'team_id' => null,
    ]);
});

it('rejects unauthenticated save request', function (): void {
    $this->postJson('/grid-presets', [
        'name' => 'Test',
        'grid_name' => 'users',
        'filters' => '[]',
        'sorting' => '[]',
    ])
        ->assertUnauthorized();
});

// --- DeleteGridPresetController ---

it('deletes own grid preset', function (): void {
    $userModel = presetUser();
    $presetId = '550e8400-e29b-41d4-a716-446655440f20';

    GridPresetModel::create([
        'id' => $presetId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'To Delete',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($userModel)
        ->deleteJson('/grid-presets/'.$presetId)
        ->assertOk();

    $this->assertDatabaseMissing('grid_presets', ['id' => $presetId]);
});

it('returns 404 when deleting non-existent preset', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->deleteJson('/grid-presets/550e8400-e29b-41d4-a716-446655440fff')
        ->assertNotFound();
});

it('returns 403 when deleting another user preset', function (): void {
    $userModel = presetUser();
    $other = otherPresetUser();
    $presetId = '550e8400-e29b-41d4-a716-446655440f30';

    GridPresetModel::create([
        'id' => $presetId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'Not Yours',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($other)
        ->deleteJson('/grid-presets/'.$presetId)
        ->assertForbidden();

    $this->assertDatabaseHas('grid_presets', ['id' => $presetId]);
});

it('rejects unauthenticated delete request', function (): void {
    $this->deleteJson('/grid-presets/550e8400-e29b-41d4-a716-446655440f99')
        ->assertUnauthorized();
});

// --- SetDefaultGridPresetController ---

it('sets a preset as default', function (): void {
    $userModel = presetUser();
    $presetId = '550e8400-e29b-41d4-a716-446655440f40';

    GridPresetModel::create([
        'id' => $presetId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'My Default',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($userModel)
        ->postJson('/grid-presets/'.$presetId.'/default', [
            'grid_name' => 'users',
        ])
        ->assertOk();

    $this->assertDatabaseHas('grid_presets', [
        'id' => $presetId,
        'is_default' => true,
    ]);
});

it('clears previous default when setting new default', function (): void {
    $userModel = presetUser();
    $oldDefaultId = '550e8400-e29b-41d4-a716-446655440f50';
    $newDefaultId = '550e8400-e29b-41d4-a716-446655440f51';

    GridPresetModel::create([
        'id' => $oldDefaultId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'Old Default',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => true,
        'position' => 0,
    ]);

    GridPresetModel::create([
        'id' => $newDefaultId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'New Default',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 1,
    ]);

    $this->actingAs($userModel)
        ->postJson('/grid-presets/'.$newDefaultId.'/default', [
            'grid_name' => 'users',
        ])
        ->assertOk();

    $this->assertDatabaseHas('grid_presets', [
        'id' => $oldDefaultId,
        'is_default' => false,
    ]);
    $this->assertDatabaseHas('grid_presets', [
        'id' => $newDefaultId,
        'is_default' => true,
    ]);
});

it('validates grid_name is required when setting default', function (): void {
    $userModel = presetUser();
    $presetId = '550e8400-e29b-41d4-a716-446655440f60';

    GridPresetModel::create([
        'id' => $presetId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'Test',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($userModel)
        ->postJson('/grid-presets/'.$presetId.'/default', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['grid_name']);
});

it('validates grid_name max length when setting default', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets/550e8400-e29b-41d4-a716-446655440f60/default', [
            'grid_name' => str_repeat('x', 101),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['grid_name']);
});

it('returns 404 when setting default on non-existent preset', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->postJson('/grid-presets/550e8400-e29b-41d4-a716-446655440fff/default', [
            'grid_name' => 'users',
        ])
        ->assertNotFound();
});

it('returns 403 when setting default on another user preset', function (): void {
    $userModel = presetUser();
    $other = otherPresetUser();
    $presetId = '550e8400-e29b-41d4-a716-446655440f70';

    GridPresetModel::create([
        'id' => $presetId,
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'Not Yours',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($other)
        ->postJson('/grid-presets/'.$presetId.'/default', [
            'grid_name' => 'users',
        ])
        ->assertForbidden();
});

it('rejects unauthenticated set-default request', function (): void {
    $this->postJson('/grid-presets/550e8400-e29b-41d4-a716-446655440f99/default', [
        'grid_name' => 'users',
    ])
        ->assertUnauthorized();
});

// --- ListGridPresetsApiController ---

it('lists grid presets for authenticated user', function (): void {
    $userModel = presetUser();

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f80',
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'Users Preset',
        'filters' => '{"status":"active"}',
        'sorting' => '{"column":"name"}',
        'search' => 'test',
        'is_default' => true,
        'position' => 0,
    ]);

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f81',
        'user_id' => $userModel->id,
        'grid_name' => 'teams',
        'name' => 'Teams Preset',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets?grid_name=users')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', '550e8400-e29b-41d4-a716-446655440f80')
        ->assertJsonPath('data.0.name', 'Users Preset')
        ->assertJsonPath('data.0.search', 'test')
        ->assertJsonPath('data.0.is_default', true)
        ->assertJsonPath('data.0.position', 0);

    /** @var array<string, mixed> $preset */
    $preset = $response->json('data.0');
    /** @var string $filters */
    $filters = $preset['filters'];
    /** @var string $sorting */
    $sorting = $preset['sorting'];
    expect(json_decode($filters, true))->toBe(['status' => 'active']);
    expect(json_decode($sorting, true))->toBe(['column' => 'name']);
});

it('returns empty list when no presets match grid_name', function (): void {
    $userModel = presetUser();

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f82',
        'user_id' => $userModel->id,
        'grid_name' => 'teams',
        'name' => 'Teams Only',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets?grid_name=users')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('does not return other user presets', function (): void {
    $userModel = presetUser();
    $other = otherPresetUser();

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f83',
        'user_id' => $other->id,
        'grid_name' => 'users',
        'name' => 'Other Preset',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets?grid_name=users')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('validates grid_name is required for listing presets', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['grid_name']);
});

it('validates grid_name max length for listing presets', function (): void {
    $userModel = presetUser();

    $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets?grid_name='.str_repeat('x', 101))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['grid_name']);
});

it('rejects unauthenticated list request', function (): void {
    $this->getJson('/internal-api/grid-presets?grid_name=users')
        ->assertUnauthorized();
});

// --- Shared preset visibility ---

it('lists global presets alongside personal presets', function (): void {
    $userModel = presetUser();
    $other = otherPresetUser();

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fa0',
        'user_id' => $userModel->id,
        'grid_name' => 'users',
        'name' => 'My Personal',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
        'scope' => 'personal',
    ]);

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fa1',
        'user_id' => $other->id,
        'grid_name' => 'users',
        'name' => 'Global Preset',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
        'scope' => 'global',
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets?grid_name=users')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'Global Preset')
        ->assertJsonPath('data.0.scope', 'global')
        ->assertJsonPath('data.0.is_owner', false)
        ->assertJsonPath('data.1.name', 'My Personal')
        ->assertJsonPath('data.1.scope', 'personal')
        ->assertJsonPath('data.1.is_owner', true);
});

it('lists team presets for user team members', function (): void {
    $userModel = presetUser();
    $other = otherPresetUser();

    $team = TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fb0',
        'name' => 'Preset Team',
        'slug' => 'preset-team',
        'description' => 'Test',
    ]);

    TeamMemberModel::create([
        'team_id' => $team->id,
        'user_id' => $userModel->id,
        'joined_at' => now(),
    ]);

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fa2',
        'user_id' => $other->id,
        'grid_name' => 'users',
        'name' => 'Team Preset',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
        'scope' => 'team',
        'team_id' => $team->id,
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets?grid_name=users')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Team Preset')
        ->assertJsonPath('data.0.scope', 'team')
        ->assertJsonPath('data.0.is_owner', false);
});

it('does not list team presets for non-member users', function (): void {
    $userModel = presetUser();
    $other = otherPresetUser();

    $team = TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fb1',
        'name' => 'Other Team',
        'slug' => 'other-team-presets',
        'description' => 'Test',
    ]);

    GridPresetModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fa3',
        'user_id' => $other->id,
        'grid_name' => 'users',
        'name' => 'Hidden Team Preset',
        'filters' => '[]',
        'sorting' => '[]',
        'search' => '',
        'is_default' => false,
        'position' => 0,
        'scope' => 'team',
        'team_id' => $team->id,
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/grid-presets?grid_name=users')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
