<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\GridPreset\Exception\GridPresetNotFoundException;
use App\Domain\GridPreset\Exception\GridPresetOwnershipException;
use App\Domain\GridPreset\Exception\GridPresetScopePermissionException;
use App\Domain\GridPreset\Exception\InvalidGridPresetIdException;

it('grid preset not found has correct status code', function (): void {
    $exception = new GridPresetNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(HttpStatus::NOT_FOUND)
        ->and($exception->presetId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('grid preset ownership has correct status code', function (): void {
    $exception = new GridPresetOwnershipException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(HttpStatus::FORBIDDEN)
        ->and($exception->presetId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('invalid grid preset id has correct status code', function (): void {
    $exception = new InvalidGridPresetIdException('not-uuid');

    expect($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY)
        ->and($exception->invalidValue)->toBe('not-uuid');
});

it('invalid grid preset id has translatable user message', function (): void {
    $exception = new InvalidGridPresetIdException('not-uuid');
    $translator = new Tests\Helper\FakeTranslator;

    expect($exception->userMessage($translator))->toBe('messages.exceptions.invalid_id');
});

it('grid preset scope permission has correct status code', function (): void {
    $exception = new GridPresetScopePermissionException('global');

    expect($exception->statusCode())->toBe(HttpStatus::FORBIDDEN)
        ->and($exception->scope)->toBe('global');
});

it('grid preset scope permission has translatable user message', function (): void {
    $exception = new GridPresetScopePermissionException('team');
    $translator = new Tests\Helper\FakeTranslator;

    expect($exception->userMessage($translator))->toBe('messages.exceptions.grid_preset_scope_permission');
});
