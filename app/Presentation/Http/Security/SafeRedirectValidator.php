<?php

declare(strict_types=1);

namespace App\Presentation\Http\Security;

final readonly class SafeRedirectValidator
{
    public static function isSafe(string $url, string $allowedHost): bool
    {
        if (str_starts_with($url, '//')) {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return true;
        }

        $parsed = parse_url($url);

        if ($parsed === false || ! isset($parsed['host'])) {
            return false;
        }

        return $parsed['host'] === $allowedHost;
    }
}
