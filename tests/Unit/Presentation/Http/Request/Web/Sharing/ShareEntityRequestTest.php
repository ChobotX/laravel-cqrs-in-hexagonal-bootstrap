<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Web\Sharing\ShareEntityRequest;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

/** @param array<string, mixed> $data */
function shareEntityValidator(array $data): Illuminate\Validation\Validator
{
    $rules = (new ShareEntityRequest)->rules();
    $translator = new Translator(new ArrayLoader, 'en');
    $factory = new Factory($translator);

    return $factory->make($data, $rules);
}

it('rules require grantee_user_id as UUID', function (): void {
    $rules = (new ShareEntityRequest)->rules();

    expect($rules)->toHaveKey('grantee_user_id')
        ->and($rules['grantee_user_id'])->toContain('required')
        ->and($rules['grantee_user_id'])->toContain('uuid');
});

it('passes validation with a valid UUID', function (): void {
    $validator = shareEntityValidator([
        'grantee_user_id' => '550e8400-e29b-41d4-a716-446655440001',
    ]);

    expect($validator->passes())->toBeTrue();
});

it('fails validation when grantee_user_id is missing', function (): void {
    $validator = shareEntityValidator([]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('grantee_user_id'))->toBeTrue();
});

it('fails validation when grantee_user_id is not a UUID', function (): void {
    $validator = shareEntityValidator([
        'grantee_user_id' => 'not-a-uuid',
    ]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('grantee_user_id'))->toBeTrue();
});

it('granteeUserId returns the grantee_user_id string value', function (): void {
    $request = new ShareEntityRequest;
    $request->merge(['grantee_user_id' => '550e8400-e29b-41d4-a716-446655440001']);

    expect($request->granteeUserId())->toBe('550e8400-e29b-41d4-a716-446655440001');
});
