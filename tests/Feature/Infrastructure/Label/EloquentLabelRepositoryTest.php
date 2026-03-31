<?php

declare(strict_types=1);

use App\Domain\Label\Label;
use App\Domain\Label\LabelId;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use App\Infrastructure\Eloquent\Label\EloquentLabelRepository;
use App\Infrastructure\Eloquent\Label\LabelMapper;
use Illuminate\Support\Facades\DB;

function labelRepo(): EloquentLabelRepository
{
    return new EloquentLabelRepository(new LabelMapper);
}

function makeLabel(string $id, string $namespace, string $name): Label
{
    return new Label(
        id: new LabelId($id),
        namespace: new LabelNamespace($namespace),
        name: new LabelName($name),
    );
}

it('creates and finds a label by id', function (): void {
    $eloquentLabelRepository = labelRepo();
    $label = makeLabel('550e8400-e29b-41d4-a716-44665544e000', 'users', 'VIP');

    $eloquentLabelRepository->create($label);
    $found = $eloquentLabelRepository->findById($label->id);

    expect($found)->not->toBeNull()
        ->and($found->id->value)->toBe('550e8400-e29b-41d4-a716-44665544e000')
        ->and($found->namespace->value)->toBe('users')
        ->and($found->name->value)->toBe('VIP');
});

it('returns null for non-existent id', function (): void {
    $found = labelRepo()->findById(new LabelId('550e8400-e29b-41d4-a716-44665544e0ff'));

    expect($found)->toBeNull();
});

it('finds label by namespace and name', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e001', 'users', 'Internal'));

    $found = $eloquentLabelRepository->findByNamespaceAndName(new LabelNamespace('users'), new LabelName('Internal'));

    expect($found)->not->toBeNull()
        ->and($found->name->value)->toBe('Internal');
});

it('returns null when namespace and name not found', function (): void {
    $found = labelRepo()->findByNamespaceAndName(new LabelNamespace('users'), new LabelName('Missing'));

    expect($found)->toBeNull();
});

it('deletes a label', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e002', 'users', 'ToDelete'));

    $eloquentLabelRepository->delete(new LabelId('550e8400-e29b-41d4-a716-44665544e002'));

    expect($eloquentLabelRepository->findById(new LabelId('550e8400-e29b-41d4-a716-44665544e002')))->toBeNull();
});

it('searches labels with term filtering', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e010', 'users', 'VIP'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e011', 'users', 'Internal'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e012', 'users', 'Visitor'));

    $results = $eloquentLabelRepository->search(new LabelNamespace('users'), 'VI', [], 50);

    expect($results)->toHaveCount(2);

    $names = array_map(fn (Label $label): string => $label->name->value, $results);
    expect($names)->toContain('VIP')
        ->toContain('Visitor');
});

it('searches labels filtered by namespace', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e020', 'users', 'VIP'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e021', 'teams', 'Engineering'));

    $results = $eloquentLabelRepository->search(new LabelNamespace('users'), '', [], 50);

    expect($results)->toHaveCount(1)
        ->and($results[0]->name->value)->toBe('VIP');
});

it('searches with exclude filter', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e030', 'users', 'VIP'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e031', 'users', 'Internal'));

    $results = $eloquentLabelRepository->search(
        new LabelNamespace('users'),
        '',
        ['550e8400-e29b-41d4-a716-44665544e030'],
        50,
    );

    expect($results)->toHaveCount(1)
        ->and($results[0]->name->value)->toBe('Internal');
});

it('respects search limit', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e040', 'users', 'Alpha'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e041', 'users', 'Beta'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e042', 'users', 'Gamma'));

    $results = $eloquentLabelRepository->search(new LabelNamespace('users'), '', [], 2);

    expect($results)->toHaveCount(2);
});

it('assigns a label to a labelable', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e050', 'users', 'VIP'));

    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e050', '550e8400-e29b-41d4-a716-44665544f000');

    $count = DB::table('label_assignments')
        ->where('label_id', '550e8400-e29b-41d4-a716-44665544e050')
        ->where('labelable_id', '550e8400-e29b-41d4-a716-44665544f000')
        ->count();

    expect($count)->toBe(1);
});

it('duplicate assignment is ignored via insertOrIgnore', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e051', 'users', 'VIP'));

    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e051', '550e8400-e29b-41d4-a716-44665544f001');
    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e051', '550e8400-e29b-41d4-a716-44665544f001');

    $count = DB::table('label_assignments')
        ->where('label_id', '550e8400-e29b-41d4-a716-44665544e051')
        ->where('labelable_id', '550e8400-e29b-41d4-a716-44665544f001')
        ->count();

    expect($count)->toBe(1);
});

it('removes an assignment', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e060', 'users', 'Removable'));

    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e060', '550e8400-e29b-41d4-a716-44665544f010');
    $eloquentLabelRepository->removeAssignment('550e8400-e29b-41d4-a716-44665544e060', '550e8400-e29b-41d4-a716-44665544f010');

    $count = DB::table('label_assignments')
        ->where('label_id', '550e8400-e29b-41d4-a716-44665544e060')
        ->where('labelable_id', '550e8400-e29b-41d4-a716-44665544f010')
        ->count();

    expect($count)->toBe(0);
});

it('hasAssignments returns true when assignments exist', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e070', 'users', 'Assigned'));

    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e070', '550e8400-e29b-41d4-a716-44665544f020');

    expect($eloquentLabelRepository->hasAssignments(new LabelId('550e8400-e29b-41d4-a716-44665544e070')))->toBeTrue();
});

it('hasAssignments returns false when no assignments exist', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e071', 'users', 'Unassigned'));

    expect($eloquentLabelRepository->hasAssignments(new LabelId('550e8400-e29b-41d4-a716-44665544e071')))->toBeFalse();
});

it('finds labels by labelable id', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e080', 'users', 'Alpha'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e081', 'users', 'Beta'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e082', 'users', 'Gamma'));

    $labelableId = '550e8400-e29b-41d4-a716-44665544f030';
    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e080', $labelableId);
    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e081', $labelableId);

    $labels = $eloquentLabelRepository->findByLabelableId($labelableId);

    expect($labels)->toHaveCount(2);

    $names = array_map(fn (Label $label): string => $label->name->value, $labels);
    expect($names)->toBe(['Alpha', 'Beta']);
});

it('returns empty when labelable has no labels', function (): void {
    $labels = labelRepo()->findByLabelableId('550e8400-e29b-41d4-a716-44665544f0ff');

    expect($labels)->toBe([]);
});

it('removes all assignments for labelable and returns orphaned ids', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e090', 'users', 'Orphan'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e091', 'users', 'Shared'));

    $labelableA = '550e8400-e29b-41d4-a716-44665544f040';
    $labelableB = '550e8400-e29b-41d4-a716-44665544f041';

    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e090', $labelableA);
    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e091', $labelableA);
    $eloquentLabelRepository->assignLabel('550e8400-e29b-41d4-a716-44665544e091', $labelableB);

    $orphanedIds = $eloquentLabelRepository->removeAllAssignmentsForLabelable($labelableA);

    expect($orphanedIds)->toBe(['550e8400-e29b-41d4-a716-44665544e090']);

    $assignmentsForA = DB::table('label_assignments')
        ->where('labelable_id', $labelableA)
        ->count();
    expect($assignmentsForA)->toBe(0);

    $assignmentsForB = DB::table('label_assignments')
        ->where('labelable_id', $labelableB)
        ->count();
    expect($assignmentsForB)->toBe(1);
});

it('returns empty orphans when labelable has no assignments', function (): void {
    $orphanedIds = labelRepo()->removeAllAssignmentsForLabelable('550e8400-e29b-41d4-a716-44665544f0ee');

    expect($orphanedIds)->toBe([]);
});

it('deletes labels by ids', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e0a0', 'users', 'DeleteA'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e0a1', 'users', 'DeleteB'));
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e0a2', 'users', 'Keep'));

    $eloquentLabelRepository->deleteByIds([
        '550e8400-e29b-41d4-a716-44665544e0a0',
        '550e8400-e29b-41d4-a716-44665544e0a1',
    ]);

    expect($eloquentLabelRepository->findById(new LabelId('550e8400-e29b-41d4-a716-44665544e0a0')))->toBeNull()
        ->and($eloquentLabelRepository->findById(new LabelId('550e8400-e29b-41d4-a716-44665544e0a1')))->toBeNull()
        ->and($eloquentLabelRepository->findById(new LabelId('550e8400-e29b-41d4-a716-44665544e0a2')))->not->toBeNull();
});

it('deleteByIds does nothing with empty array', function (): void {
    $eloquentLabelRepository = labelRepo();
    $eloquentLabelRepository->create(makeLabel('550e8400-e29b-41d4-a716-44665544e0b0', 'users', 'Survive'));

    $eloquentLabelRepository->deleteByIds([]);

    expect($eloquentLabelRepository->findById(new LabelId('550e8400-e29b-41d4-a716-44665544e0b0')))->not->toBeNull();
});
