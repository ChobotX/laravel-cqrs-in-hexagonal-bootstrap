<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use App\Infrastructure\Sso\Exception\SsoDiscoveryFailedException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;

use function str_starts_with;

/**
 * Fetches the OIDC discovery document for an IdP each time it is needed.
 */
final readonly class OidcDiscoveryClient
{
    public function __construct(
        private HttpFactory $httpFactory,
    ) {}

    /** @return array<string, string> */
    public function fetch(string $discoveryUrl): array
    {
        if (! str_starts_with($discoveryUrl, 'https://')) {
            throw new SsoConfigurationInvalidException('OIDC discovery URL must be an HTTPS URL.');
        }

        try {
            $response = $this->httpFactory->get($discoveryUrl);
        } catch (ConnectionException $connectionException) {
            throw new SsoDiscoveryFailedException('OIDC discovery endpoint unreachable: '.$connectionException->getMessage(), $connectionException->getCode(), $connectionException);
        }

        if (! $response->successful()) {
            throw new SsoDiscoveryFailedException('OIDC discovery endpoint returned non-2xx status.');
        }

        /** @var array<string, string> $document */
        $document = (array) $response->json();

        return $document + ['authorization_endpoint' => '', 'token_endpoint' => ''];
    }
}
