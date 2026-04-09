<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Tenancy\Exception\InvalidTenantNameException;
use App\Domain\Tenancy\Exception\TenantNotFoundException;

function tenantTestTranslator(): Translator
{
    return new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key.':'.implode(',', $params);
        }

        public function locale(): string
        {
            return 'en';
        }
    };
}

it('TenantNotFoundException has status 404 and translates', function (): void {
    $e = new TenantNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($e->statusCode())->toBe(404)
        ->and($e->getMessage())->toContain('550e8400-e29b-41d4-a716-446655440000')
        ->and($e->userMessage(tenantTestTranslator()))->toBe('messages.exceptions.tenant_not_found:550e8400-e29b-41d4-a716-446655440000')
        ->and($e->id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('InvalidTenantNameException has status 422 and translates', function (): void {
    $e = new InvalidTenantNameException;

    expect($e->statusCode())->toBe(422)
        ->and($e->getMessage())->toBe('Tenant name must not be empty.')
        ->and($e->userMessage(tenantTestTranslator()))->toBe('messages.exceptions.invalid_tenant_name:');
});
