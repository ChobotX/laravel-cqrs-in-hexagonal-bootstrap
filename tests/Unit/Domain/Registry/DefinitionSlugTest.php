<?php

declare(strict_types=1);

use App\Domain\Registry\Exception\InvalidDefinitionSlugException;
use App\Domain\Registry\ValueObject\DefinitionSlug;

it('can be constructed with a valid slug', function (string $value): void {
    $slug = new DefinitionSlug($value);

    expect($slug->value)->toBe($value);
})->with(['users', 'teams', 'my_definition', 'a', 'abc_def', 'a123']);

it('rejects uppercase characters', function (): void {
    new DefinitionSlug('Users');
})->throws(InvalidDefinitionSlugException::class, 'Value [Users] is not a valid definition slug.');

it('rejects spaces', function (): void {
    new DefinitionSlug('my slug');
})->throws(InvalidDefinitionSlugException::class, 'Value [my slug] is not a valid definition slug.');

it('rejects a slug starting with a number', function (): void {
    new DefinitionSlug('1users');
})->throws(InvalidDefinitionSlugException::class, 'Value [1users] is not a valid definition slug.');

it('rejects an empty string', function (): void {
    new DefinitionSlug('');
})->throws(InvalidDefinitionSlugException::class, 'Value [] is not a valid definition slug.');

it('rejects hyphens', function (): void {
    new DefinitionSlug('my-slug');
})->throws(InvalidDefinitionSlugException::class, 'Value [my-slug] is not a valid definition slug.');

it('can compare equality with another DefinitionSlug', function (): void {
    $slug1 = new DefinitionSlug('users');
    $slug2 = new DefinitionSlug('users');
    $slug3 = new DefinitionSlug('teams');

    expect($slug1->equals($slug2))->toBeTrue()
        ->and($slug1->equals($slug3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $slug = new DefinitionSlug('users');

    expect((string) $slug)->toBe('users');
});
