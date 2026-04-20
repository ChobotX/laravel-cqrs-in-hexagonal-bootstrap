<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;

use function is_string;

/**
 * Fetches the verified primary email for a GitHub user when the basic profile
 * does not include one (private profiles).
 */
final readonly class GithubEmailFetcher
{
    private const string EMAILS_URL = 'https://api.github.com/user/emails';

    private const string USER_AGENT = 'laravel-cqrs-hexagonal-bootstrap';

    public function __construct(
        private HttpFactory $httpFactory,
    ) {}

    public function fetch(string $accessToken): ?string
    {
        try {
            $response = $this->httpFactory
                ->withToken($accessToken)
                ->withHeaders(['Accept' => 'application/vnd.github+json', 'User-Agent' => self::USER_AGENT])
                ->get(self::EMAILS_URL);
        } catch (ConnectionException) {
            // @silent: fall back to null so the caller raises missing_required_claims.
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        /** @var list<array{email?: string, primary?: bool, verified?: bool}> $rows */
        $rows = (array) $response->json();

        return $this->pickPrimary($rows);
    }

    /** @param list<array{email?: string, primary?: bool, verified?: bool}> $rows */
    private function pickPrimary(array $rows): ?string
    {
        foreach ($rows as $row) {
            if ($this->isPrimaryVerified($row)) {
                return $row['email'] ?? null;
            }
        }

        return null;
    }

    /** @param array{email?: string, primary?: bool, verified?: bool} $row */
    private function isPrimaryVerified(array $row): bool
    {
        $email = $row['email'] ?? null;

        return ($row['primary'] ?? false) === true
            && ($row['verified'] ?? false) === true
            && is_string($email)
            && $email !== '';
    }
}
