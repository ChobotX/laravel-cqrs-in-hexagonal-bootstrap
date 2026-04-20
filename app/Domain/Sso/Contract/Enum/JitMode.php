<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Enum;

/**
 * Just-in-time provisioning policy applied when an SSO callback yields an unknown user.
 */
enum JitMode: string
{
    /** Activate an already-invited (pending) user matched by email; reject otherwise. */
    case InvitedOnly = 'invited_only';

    /** Create a new user on first successful SSO login (subject to allowed_email_domains). */
    case AutoCreate = 'auto_create';

    /** Refuse login unless an admin has previously linked the SSO subject to a user. */
    case LinkedOnly = 'linked_only';
}
