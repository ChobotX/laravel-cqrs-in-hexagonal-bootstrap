<?php

declare(strict_types=1);

use App\Presentation\Http\Security\SafeRedirectValidator;

it('allows relative path', function (): void {
    expect(SafeRedirectValidator::isSafe('/users/create', 'localhost'))->toBeTrue();
});

it('allows same-host absolute URL', function (): void {
    expect(SafeRedirectValidator::isSafe('http://localhost/users', 'localhost'))->toBeTrue();
});

it('rejects different-host URL', function (): void {
    expect(SafeRedirectValidator::isSafe('https://evil.com/phish', 'localhost'))->toBeFalse();
});

it('rejects protocol-relative URL', function (): void {
    expect(SafeRedirectValidator::isSafe('//evil.com/phish', 'localhost'))->toBeFalse();
});

it('rejects URL with no host and no path', function (): void {
    expect(SafeRedirectValidator::isSafe('javascript:alert(1)', 'localhost'))->toBeFalse();
});

it('allows root path', function (): void {
    expect(SafeRedirectValidator::isSafe('/', 'localhost'))->toBeTrue();
});

it('allows path with query string', function (): void {
    expect(SafeRedirectValidator::isSafe('/users?page=2', 'localhost'))->toBeTrue();
});

it('rejects backslash URL', function (): void {
    expect(SafeRedirectValidator::isSafe('\evil.com', 'localhost'))->toBeFalse();
});

it('rejects data URI scheme', function (): void {
    expect(SafeRedirectValidator::isSafe('data:text/html,<script>alert(1)</script>', 'localhost'))->toBeFalse();
});
