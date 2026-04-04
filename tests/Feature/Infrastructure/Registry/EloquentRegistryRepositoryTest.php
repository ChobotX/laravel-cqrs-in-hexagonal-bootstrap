<?php

declare(strict_types=1);

use App\Application\Pagination\Pagination;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\EntryId;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\Definition;
use App\Domain\Registry\DefinitionName;
use App\Domain\Registry\DefinitionNamespace;
use App\Domain\Registry\DefinitionSlug;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\Entry;
use App\Domain\Registry\EntryTitle;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Schema\StringField;
use App\Domain\Registry\VersionNumber;
use App\Infrastructure\Eloquent\Registry\DefinitionModel;
use App\Infrastructure\Eloquent\Registry\DefinitionVersionModel;
use App\Infrastructure\Eloquent\Registry\EloquentDefinitionRepository;
use App\Infrastructure\Eloquent\Registry\EloquentDefinitionVersionRepository;
use App\Infrastructure\Eloquent\Registry\EloquentEntryRepository;
use App\Infrastructure\Eloquent\Registry\EntryModel;

function defRepo(): EloquentDefinitionRepository
{
    return app(EloquentDefinitionRepository::class);
}

function versionRepo(): EloquentDefinitionVersionRepository
{
    return app(EloquentDefinitionVersionRepository::class);
}

function entryRepo(): EloquentEntryRepository
{
    return app(EloquentEntryRepository::class);
}

function makeDef(string $id, string $ns, string $slug, string $name): Definition
{
    return new Definition(
        new DefinitionId($id),
        new DefinitionNamespace($ns),
        new DefinitionSlug($slug),
        new DefinitionName($name),
    );
}

// --- Definition Repository ---

it('returns null for non-existent definition by id', function (): void {
    expect(defRepo()->findById(new DefinitionId('550e8400-e29b-41d4-a716-446655440b00')))->toBeNull();
});

it('creates and finds a definition', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b01', 'enumerations', 'color', 'Color');
    defRepo()->create($definition);

    $found = defRepo()->findById(new DefinitionId('550e8400-e29b-41d4-a716-446655440b01'));
    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Color');
});

it('throws on duplicate definition', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b02', 'enumerations', 'dupe', 'Dupe');
    defRepo()->create($definition);
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b03', 'enumerations', 'dupe', 'Dupe2'));
})->throws(DefinitionAlreadyExistsException::class);

it('finds definition by namespace and slug', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b04', 'enumerations', 'size', 'Size');
    defRepo()->create($definition);

    $found = defRepo()->findByNamespaceAndSlug(new DefinitionNamespace('enumerations'), new DefinitionSlug('size'));
    expect($found)->not->toBeNull()
        ->and($found->id->value)->toBe('550e8400-e29b-41d4-a716-446655440b04');
});

it('returns null for non-existent namespace-slug', function (): void {
    expect(defRepo()->findByNamespaceAndSlug(new DefinitionNamespace('no'), new DefinitionSlug('such')))->toBeNull();
});

it('paginates definitions with namespace filter', function (): void {
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b05', 'cat_a', 'one', 'One'));
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b06', 'cat_b', 'two', 'Two'));

    $paginatedResult = defRepo()->allPaginated(new Pagination(1, 10), new DefinitionNamespace('cat_a'));
    expect($paginatedResult->total)->toBe(1)
        ->and($paginatedResult->items)->toHaveCount(1);
});

it('updates a definition name', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b07', 'enumerations', 'upd', 'Old');
    defRepo()->create($definition);

    $updated = new Definition($definition->id, $definition->namespace, $definition->slug, new DefinitionName('New'));
    defRepo()->update($updated);

    expect(defRepo()->findById($definition->id)->name->value)->toBe('New');
});

it('deletes a definition', function (): void {
    $definition = makeDef('550e8400-e29b-41d4-a716-446655440b08', 'enumerations', 'del', 'Del');
    defRepo()->create($definition);

    defRepo()->delete($definition->id);

    expect(defRepo()->findById($definition->id))->toBeNull();
});

it('returns unique sorted namespaces', function (): void {
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b09', 'zebra', 'z', 'Z'));
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b0a', 'alpha', 'a', 'A'));
    defRepo()->create(makeDef('550e8400-e29b-41d4-a716-446655440b0b', 'alpha', 'b', 'B'));

    expect(defRepo()->allNamespaces())->toBe(['alpha', 'zebra']);
});

// --- Definition Version Repository ---

it('returns null for non-existent version', function (): void {
    expect(versionRepo()->findById(new DefinitionVersionId('550e8400-e29b-41d4-a716-446655440c00')))->toBeNull();
});

it('creates a version and finds by definition and version number', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c10', 'namespace' => 'test', 'slug' => 'vertest', 'name' => 'VerTest']);

    $schema = new Schema([new StringField('x', 'X')]);
    $version = new DefinitionVersion(
        new DefinitionVersionId('550e8400-e29b-41d4-a716-446655440c11'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c10'),
        new VersionNumber(1),
        $schema,
        VersionStatus::Draft,
    );

    versionRepo()->create($version);

    $found = versionRepo()->findByDefinitionAndVersion(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c10'),
        new VersionNumber(1),
    );
    expect($found)->not->toBeNull()
        ->and($found->status)->toBe(VersionStatus::Draft);
});

it('returns null for non-existent definition-version pair', function (): void {
    expect(versionRepo()->findByDefinitionAndVersion(
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c99'),
        new VersionNumber(1),
    ))->toBeNull();
});

it('returns null when no active version exists', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c40', 'namespace' => 'test', 'slug' => 'noactive', 'name' => 'NoActive']);

    expect(versionRepo()->findActiveByDefinition(new DefinitionId('550e8400-e29b-41d4-a716-446655440c40')))->toBeNull();
});

// --- Entry Repository ---

it('creates and finds an entry', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c20', 'namespace' => 'test', 'slug' => 'enttest', 'name' => 'EntTest']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c21',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c20',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => ['x' => ['type' => 'string']], 'required' => []],
        'status' => VersionStatus::Active,
    ]);

    $entry = new Entry(
        new EntryId('550e8400-e29b-41d4-a716-446655440c22'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c20'),
        new VersionNumber(1),
        new DefinitionNamespace('test'),
        new EntryTitle('Test Entry'),
        ['x' => 'val'],
    );

    entryRepo()->create($entry);

    $found = entryRepo()->findById(new EntryId('550e8400-e29b-41d4-a716-446655440c22'));
    expect($found)->not->toBeNull()
        ->and($found->title->value)->toBe('Test Entry');
});

it('returns null for non-existent entry', function (): void {
    expect(entryRepo()->findById(new EntryId('550e8400-e29b-41d4-a716-446655440c99')))->toBeNull();
});

it('silently returns when updating non-existent entry', function (): void {
    $entry = new Entry(
        new EntryId('550e8400-e29b-41d4-a716-446655440c99'),
        new DefinitionId('550e8400-e29b-41d4-a716-446655440c20'),
        new VersionNumber(1),
        new DefinitionNamespace('test'),
        new EntryTitle('Ghost'),
        ['x' => 'val'],
    );

    entryRepo()->update($entry);

    expect(entryRepo()->findById(new EntryId('550e8400-e29b-41d4-a716-446655440c99')))->toBeNull();
});

it('checks if entries exist by definition', function (): void {
    DefinitionModel::create(['id' => '550e8400-e29b-41d4-a716-446655440c30', 'namespace' => 'test', 'slug' => 'exists', 'name' => 'Exists']);
    DefinitionVersionModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c31',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c30',
        'version' => 1,
        'body' => ['type' => 'object', 'properties' => [], 'required' => []],
        'status' => VersionStatus::Active,
    ]);

    expect(entryRepo()->existsByDefinition(new DefinitionId('550e8400-e29b-41d4-a716-446655440c30')))->toBeFalse();

    EntryModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440c32',
        'definition_id' => '550e8400-e29b-41d4-a716-446655440c30',
        'definition_version' => 1,
        'namespace' => 'test',
        'title' => 'X',
        'data' => [],
    ]);

    expect(entryRepo()->existsByDefinition(new DefinitionId('550e8400-e29b-41d4-a716-446655440c30')))->toBeTrue();
});
