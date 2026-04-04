<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Infrastructure\Eloquent\Registry\DefinitionModel;
use App\Infrastructure\Eloquent\Registry\DefinitionVersionModel;
use App\Infrastructure\Eloquent\Registry\EntryModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function entryWebUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e00',
        'name' => 'Entry Admin',
        'email' => 'entryadmin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

function seedEntryDefinition(): DefinitionModel
{
    $definition = DefinitionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e10',
        'namespace' => 'enumerations',
        'slug' => 'car_brand',
        'name' => 'Car Brand',
    ]);

    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e11',
        'definition_id' => $definition->id,
        'version' => 1,
        'body' => [
            'type' => 'object',
            'properties' => [
                'country' => ['type' => 'string', 'x-field-label' => 'Country'],
            ],
            'required' => [],
        ],
        'status' => VersionStatus::Active,
    ]);

    return $definition;
}

function seedEntry(string $id, string $definitionId, string $title): EntryModel
{
    return EntryModel::create([
        'id' => $id,
        'definition_id' => $definitionId,
        'definition_version' => 1,
        'namespace' => 'enumerations',
        'title' => $title,
        'data' => ['country' => 'Germany'],
    ]);
}

it('shows entries list page', function (): void {
    $userModel = entryWebUser();
    seedEntryDefinition();

    $this->actingAs($userModel)
        ->get('/registry/enumerations/car_brand/entries')
        ->assertOk();
});

it('returns 404 for entries list on non-existent definition', function (): void {
    $userModel = entryWebUser();

    $this->actingAs($userModel)
        ->get('/registry/nonexistent/slug/entries')
        ->assertNotFound();
});

it('shows create entry form', function (): void {
    $userModel = entryWebUser();
    seedEntryDefinition();

    $this->actingAs($userModel)
        ->get('/registry/enumerations/car_brand/entries/create')
        ->assertOk();
});

it('returns 404 for create entry on non-existent definition', function (): void {
    $userModel = entryWebUser();

    $this->actingAs($userModel)
        ->get('/registry/nonexistent/slug/entries/create')
        ->assertNotFound();
});

it('creates an entry via web', function (): void {
    $userModel = entryWebUser();
    seedEntryDefinition();

    $this->actingAs($userModel)
        ->post('/registry/enumerations/car_brand/entries', [
            'title' => 'BMW',
            'data' => ['country' => 'Germany'],
        ])->assertRedirect(route('registry.entries.index', ['enumerations', 'car_brand']));

    $this->assertDatabaseHas('entries', ['title' => 'BMW']);
});

it('validates create entry request', function (): void {
    $userModel = entryWebUser();
    seedEntryDefinition();

    $this->actingAs($userModel)
        ->post('/registry/enumerations/car_brand/entries', [
            'title' => '',
        ])->assertSessionHasErrors(['title', 'data']);
});

it('returns 404 for creating entry on non-existent definition', function (): void {
    $userModel = entryWebUser();

    $this->actingAs($userModel)
        ->post('/registry/nonexistent/slug/entries', [
            'title' => 'Test',
            'data' => ['x' => 'y'],
        ])->assertNotFound();
});

it('shows edit entry form', function (): void {
    $userModel = entryWebUser();
    $definitionModel = seedEntryDefinition();
    seedEntry('550e8400-e29b-41d4-a716-446655440e20', $definitionModel->id, 'Toyota');

    $this->actingAs($userModel)
        ->get('/registry/enumerations/car_brand/entries/550e8400-e29b-41d4-a716-446655440e20/edit')
        ->assertOk();
});

it('returns 404 for editing non-existent entry', function (): void {
    $userModel = entryWebUser();
    seedEntryDefinition();

    $this->actingAs($userModel)
        ->get('/registry/enumerations/car_brand/entries/550e8400-e29b-41d4-a716-446655440e99/edit')
        ->assertNotFound();
});

it('returns 404 for edit entry on non-existent definition', function (): void {
    $userModel = entryWebUser();

    $this->actingAs($userModel)
        ->get('/registry/nonexistent/slug/entries/550e8400-e29b-41d4-a716-446655440e20/edit')
        ->assertNotFound();
});

it('updates an entry via web', function (): void {
    $userModel = entryWebUser();
    $definitionModel = seedEntryDefinition();
    seedEntry('550e8400-e29b-41d4-a716-446655440e30', $definitionModel->id, 'Honda');

    $this->actingAs($userModel)
        ->put('/registry/enumerations/car_brand/entries/550e8400-e29b-41d4-a716-446655440e30', [
            'title' => 'Honda Updated',
            'data' => ['country' => 'Japan'],
        ])->assertRedirect(route('registry.entries.index', ['enumerations', 'car_brand']));

    $this->assertDatabaseHas('entries', ['id' => '550e8400-e29b-41d4-a716-446655440e30', 'title' => 'Honda Updated']);
});

it('validates update entry request', function (): void {
    $userModel = entryWebUser();
    $definitionModel = seedEntryDefinition();
    seedEntry('550e8400-e29b-41d4-a716-446655440e31', $definitionModel->id, 'Ford');

    $this->actingAs($userModel)
        ->put('/registry/enumerations/car_brand/entries/550e8400-e29b-41d4-a716-446655440e31', [
            'title' => '',
        ])->assertSessionHasErrors(['title', 'data']);
});

it('deletes an entry via web', function (): void {
    $userModel = entryWebUser();
    $definitionModel = seedEntryDefinition();
    seedEntry('550e8400-e29b-41d4-a716-446655440e40', $definitionModel->id, 'Delete Me');

    $this->actingAs($userModel)
        ->delete('/registry/enumerations/car_brand/entries/550e8400-e29b-41d4-a716-446655440e40')
        ->assertRedirect(route('registry.entries.index', ['enumerations', 'car_brand']));

    $this->assertDatabaseMissing('entries', ['id' => '550e8400-e29b-41d4-a716-446655440e40']);
});
