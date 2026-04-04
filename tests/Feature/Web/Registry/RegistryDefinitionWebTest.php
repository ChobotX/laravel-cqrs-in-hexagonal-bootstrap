<?php

declare(strict_types=1);

use App\Domain\Registry\VersionStatus;
use App\Infrastructure\Eloquent\Registry\DefinitionModel;
use App\Infrastructure\Eloquent\Registry\DefinitionVersionModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function registryUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440d00',
        'name' => 'Registry Admin',
        'email' => 'regadmin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

function seedDefinition(string $id, string $namespace, string $slug, string $name): DefinitionModel
{
    return DefinitionModel::create([
        'id' => $id,
        'namespace' => $namespace,
        'slug' => $slug,
        'name' => $name,
    ]);
}

function seedVersion(string $id, string $definitionId, int $version, VersionStatus $versionStatus): DefinitionVersionModel
{
    return DefinitionVersionModel::create([
        'id' => $id,
        'definition_id' => $definitionId,
        'version' => $version,
        'body' => [
            'type' => 'object',
            'properties' => [
                'color' => ['type' => 'string', 'x-field-label' => 'Color'],
            ],
            'required' => [],
        ],
        'status' => $versionStatus,
    ]);
}

it('shows definitions list page', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->get('/registry')
        ->assertOk();
});

it('redirects to page 1 when definitions page exceeds total pages', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->get('/registry?page=5&per_page=15')
        ->assertRedirect('/registry?page=1&per_page=15');
});

it('shows create definition form', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->get('/registry/create')
        ->assertOk();
});

it('creates a definition via web', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->post('/registry', [
            'namespace' => 'enumerations',
            'slug' => 'priority',
            'name' => 'Priority',
        ])->assertRedirect(route('registry.definitions.show', ['enumerations', 'priority']));

    $this->assertDatabaseHas('definitions', ['namespace' => 'enumerations', 'slug' => 'priority', 'name' => 'Priority']);
});

it('validates create definition request', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->post('/registry', [
            'namespace' => '',
            'slug' => 'INVALID',
            'name' => '',
        ])->assertSessionHasErrors(['namespace', 'slug', 'name']);
});

it('shows definition detail page', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d10', 'enumerations', 'color', 'Color');

    $this->actingAs($userModel)
        ->get('/registry/enumerations/color')
        ->assertOk()
        ->assertSee('Color');
});

it('returns 404 for non-existent definition show', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->get('/registry/nonexistent/slug')
        ->assertNotFound();
});

it('shows edit definition form', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d20', 'enumerations', 'status', 'Status');

    $this->actingAs($userModel)
        ->get('/registry/enumerations/status/edit')
        ->assertOk();
});

it('returns 404 for non-existent definition edit', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->get('/registry/nonexistent/slug/edit')
        ->assertNotFound();
});

it('updates a definition via web', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d30', 'enumerations', 'size', 'Size');

    $this->actingAs($userModel)
        ->put('/registry/enumerations/size', ['name' => 'Updated Size'])
        ->assertRedirect();

    $this->assertDatabaseHas('definitions', ['id' => '550e8400-e29b-41d4-a716-446655440d30', 'name' => 'Updated Size']);
});

it('validates update definition request', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d31', 'enumerations', 'weight', 'Weight');

    $this->actingAs($userModel)
        ->put('/registry/enumerations/weight', ['name' => ''])
        ->assertSessionHasErrors(['name']);
});

it('returns 404 for updating non-existent definition', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->put('/registry/nonexistent/slug', ['name' => 'X'])
        ->assertNotFound();
});

it('deletes a definition via web', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d40', 'enumerations', 'deleteme', 'Delete Me');

    $this->actingAs($userModel)
        ->delete('/registry/enumerations/deleteme')
        ->assertRedirect(route('registry.definitions.index'));

    $this->assertDatabaseMissing('definitions', ['id' => '550e8400-e29b-41d4-a716-446655440d40']);
});

it('returns 404 for deleting non-existent definition', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->delete('/registry/nonexistent/slug')
        ->assertNotFound();
});

it('creates a definition version via web', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d50', 'enumerations', 'versioned', 'Versioned');

    $this->actingAs($userModel)
        ->post('/registry/enumerations/versioned/versions', [
            'schema' => [
                'fields' => [
                    ['name' => 'label', 'label' => 'Label', 'type' => 'string', 'required' => true],
                ],
            ],
        ])->assertRedirect();

    $this->assertDatabaseHas('definition_versions', ['definition_id' => '550e8400-e29b-41d4-a716-446655440d50', 'version' => 1]);
});

it('validates create version request', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d51', 'enumerations', 'valversion', 'ValVersion');

    $this->actingAs($userModel)
        ->post('/registry/enumerations/valversion/versions', [
            'schema' => [],
        ])->assertSessionHasErrors(['schema.fields']);
});

it('returns 404 for creating version on non-existent definition', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->post('/registry/nonexistent/slug/versions', [
            'schema' => ['fields' => [['name' => 'a', 'label' => 'A', 'type' => 'string', 'required' => false]]],
        ])->assertNotFound();
});

it('activates a definition version via web', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d60', 'enumerations', 'activate', 'Activate');
    seedVersion('550e8400-e29b-41d4-a716-446655440d61', '550e8400-e29b-41d4-a716-446655440d60', 1, VersionStatus::Draft);

    $this->actingAs($userModel)
        ->post('/registry/enumerations/activate/versions/1/activate')
        ->assertRedirect();

    $this->assertDatabaseHas('definition_versions', ['id' => '550e8400-e29b-41d4-a716-446655440d61', 'status' => VersionStatus::Active]);
});

it('returns 404 for activating non-existent version', function (): void {
    $userModel = registryUser();
    seedDefinition('550e8400-e29b-41d4-a716-446655440d62', 'enumerations', 'noversion', 'NoVersion');

    $this->actingAs($userModel)
        ->post('/registry/enumerations/noversion/versions/99/activate')
        ->assertNotFound();
});

it('returns 404 for activating version on non-existent definition', function (): void {
    $userModel = registryUser();

    $this->actingAs($userModel)
        ->post('/registry/nonexistent/slug/versions/1/activate')
        ->assertNotFound();
});

it('redirects unauthenticated to login for registry', function (): void {
    $this->get('/registry')->assertRedirect('/login?'.http_build_query(['redirect' => '/registry']));
});
