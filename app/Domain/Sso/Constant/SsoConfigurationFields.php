<?php

declare(strict_types=1);

namespace App\Domain\Sso\Constant;

final readonly class SsoConfigurationFields
{
    public const string DISPLAY_NAME = 'display_name';

    public const string ENABLED = 'enabled';

    public const string ENFORCE = 'enforce';

    public const string JIT_MODE = 'jit_mode';

    public const string ALLOWED_EMAIL_DOMAINS = 'allowed_email_domains';

    public const string CONFIG_FINGERPRINT = 'config_fingerprint';
}
