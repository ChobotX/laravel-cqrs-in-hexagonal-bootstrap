<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Tenancy\Contract\Exception\TenantNotFoundException;

it('exposes the identifier', function (): void {
    $exception = new TenantNotFoundException('test-slug');

    expect($exception->identifier)->toBe('test-slug');
});

it('has a technical message with the identifier', function (): void {
    $exception = new TenantNotFoundException('test-slug');

    expect($exception->getMessage())->toBe('Tenant not found for identifier [test-slug].');
});

it('returns translated user message', function (): void {
    $exception = new TenantNotFoundException('test-slug');

    $translator = new class implements Translator
    {
        /**
         * @param  array<string, string|int>  $params
         */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s [identifier=%s]', $key, $params['identifier']);
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.tenant_not_found [identifier=test-slug]');
});

it('returns 404 status code', function (): void {
    $exception = new TenantNotFoundException('test-slug');

    expect($exception->statusCode())->toBe(404);
});
