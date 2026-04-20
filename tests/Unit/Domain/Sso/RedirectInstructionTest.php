<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;

it('defaults to GET redirect with no form fields', function (): void {
    $redirect = new RedirectInstruction('https://idp.example.com/authorize?x=1');

    expect($redirect->url)->toBe('https://idp.example.com/authorize?x=1')
        ->and($redirect->usesPostBinding)->toBeFalse()
        ->and($redirect->formFields)->toBe([]);
});

it('carries POST binding payloads', function (): void {
    $redirect = new RedirectInstruction('https://idp.example.com/acs', true, ['SAMLRequest' => 'x']);

    expect($redirect->usesPostBinding)->toBeTrue()
        ->and($redirect->formFields)->toBe(['SAMLRequest' => 'x']);
});
