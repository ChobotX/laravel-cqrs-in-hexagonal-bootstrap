<?php

declare(strict_types=1);

use App\Infrastructure\Registry\OpisJsonSchemaValidator;

function jsonValidator(): OpisJsonSchemaValidator
{
    return new OpisJsonSchemaValidator;
}

it('returns empty array for valid data', function (): void {
    $schema = [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
        ],
        'required' => ['name'],
    ];

    $errors = jsonValidator()->validate(['name' => 'John'], $schema);

    expect($errors)->toBe([]);
});

it('returns errors for invalid data', function (): void {
    $schema = [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'age' => ['type' => 'integer', 'minimum' => 0],
        ],
        'required' => ['name'],
    ];

    $errors = jsonValidator()->validate([], $schema);

    expect($errors)->not->toBeEmpty();
});

it('validates nested object schema', function (): void {
    $schema = [
        'type' => 'object',
        'properties' => [
            'address' => [
                'type' => 'object',
                'properties' => [
                    'city' => ['type' => 'string'],
                ],
                'required' => ['city'],
            ],
        ],
        'required' => ['address'],
    ];

    $errors = jsonValidator()->validate(['address' => []], $schema);

    expect($errors)->not->toBeEmpty();
});

it('returns formatted error messages with path', function (): void {
    $schema = [
        'type' => 'object',
        'properties' => [
            'email' => ['type' => 'string', 'format' => 'email'],
        ],
        'required' => ['email'],
    ];

    $errors = jsonValidator()->validate(['email' => 'not-an-email'], $schema);

    expect($errors)->not->toBeEmpty()
        ->and($errors[0])->toBeString();
});
