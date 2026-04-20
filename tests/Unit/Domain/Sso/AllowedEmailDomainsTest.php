<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;

it('treats empty list as unrestricted', function (): void {
    $domains = new AllowedEmailDomains([]);

    expect($domains->isUnrestricted())->toBeTrue()
        ->and($domains->permits('anyone@example.com'))->toBeTrue();
});

it('permits matching domains case-insensitively', function (): void {
    $domains = new AllowedEmailDomains(['acme.com']);

    expect($domains->permits('user@ACME.com'))->toBeTrue()
        ->and($domains->permits('user@other.com'))->toBeFalse();
});

it('rejects malformed addresses', function (): void {
    $domains = new AllowedEmailDomains(['acme.com']);

    expect($domains->permits('no-at-sign'))->toBeFalse();
});
