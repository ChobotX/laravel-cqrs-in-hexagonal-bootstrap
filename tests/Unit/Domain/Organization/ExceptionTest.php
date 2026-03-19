<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Organization\Exception\InvalidOrganizationIdException;
use App\Domain\Organization\Exception\InvalidOrganizationNameException;
use App\Domain\Organization\Exception\InvalidOrganizationSlugException;
use App\Domain\Organization\Exception\MemberAlreadyExistsException;
use App\Domain\Organization\Exception\MemberNotFoundException;
use App\Domain\Organization\Exception\OrganizationNotFoundException;
use App\Domain\Organization\Exception\OrganizationSlugAlreadyExistsException;

function testTranslator(): Translator
{
    return new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key.':'.implode(',', $params);
        }
    };
}

it('InvalidOrganizationIdException has status 422 and translates', function (): void {
    $e = new InvalidOrganizationIdException('bad-id');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('bad-id')
        ->and($e->userMessage(testTranslator()))->toBe('messages.exceptions.invalid_organization_id:bad-id')
        ->and($e->invalidValue)->toBe('bad-id');
});

it('InvalidOrganizationNameException has status 422 and translates', function (): void {
    $e = new InvalidOrganizationNameException('');

    expect($e->statusCode())->toBe(422)
        ->and($e->userMessage(testTranslator()))->toBe('messages.exceptions.invalid_organization_name:')
        ->and($e->invalidValue)->toBe('');
});

it('InvalidOrganizationSlugException has status 422 and translates', function (): void {
    $e = new InvalidOrganizationSlugException('BAD SLUG');

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toContain('BAD SLUG')
        ->and($e->userMessage(testTranslator()))->toBe('messages.exceptions.invalid_organization_slug:BAD SLUG')
        ->and($e->invalidValue)->toBe('BAD SLUG');
});

it('OrganizationNotFoundException has status 404 and translates', function (): void {
    $e = new OrganizationNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($e->statusCode())->toBe(404)
        ->and($e->getMessage())->toContain('550e8400-e29b-41d4-a716-446655440000')
        ->and($e->userMessage(testTranslator()))->toBe('messages.exceptions.organization_not_found:550e8400-e29b-41d4-a716-446655440000')
        ->and($e->id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('OrganizationSlugAlreadyExistsException has status 409 and translates', function (): void {
    $e = new OrganizationSlugAlreadyExistsException('acme');

    expect($e->statusCode())->toBe(409)
        ->and($e->getMessage())->toContain('acme')
        ->and($e->userMessage(testTranslator()))->toBe('messages.exceptions.organization_slug_already_exists:acme')
        ->and($e->slug)->toBe('acme');
});

it('MemberAlreadyExistsException has status 409 and translates', function (): void {
    $e = new MemberAlreadyExistsException('user-1', 'org-1');

    expect($e->statusCode())->toBe(409)
        ->and($e->getMessage())->toContain('user-1')
        ->and($e->userMessage(testTranslator()))->toBe('messages.exceptions.member_already_exists:')
        ->and($e->userId)->toBe('user-1')
        ->and($e->organizationId)->toBe('org-1');
});

it('MemberNotFoundException has status 404 and translates', function (): void {
    $e = new MemberNotFoundException('user-1', 'org-1');

    expect($e->statusCode())->toBe(404)
        ->and($e->getMessage())->toContain('user-1')
        ->and($e->userMessage(testTranslator()))->toBe('messages.exceptions.member_not_found:')
        ->and($e->userId)->toBe('user-1')
        ->and($e->organizationId)->toBe('org-1');
});
