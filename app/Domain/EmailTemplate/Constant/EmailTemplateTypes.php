<?php

declare(strict_types=1);

namespace App\Domain\EmailTemplate\Constant;

final readonly class EmailTemplateTypes
{
    /** @var array<string, array{name: string, description: string, variables: array<string, array{description: string, sensitive: bool, sample: string}>}> */
    public const array TYPES = [
        'user_invite' => [
            'name' => 'User Invitation',
            'description' => 'Sent when a new user is invited to the platform',
            'variables' => [
                'userName' => ['description' => "Recipient's display name", 'sensitive' => false, 'sample' => 'John Doe'],
                'link' => ['description' => 'Invitation link (expires in 72h)', 'sensitive' => true, 'sample' => 'https://example.com/invite/abc123'],
                'tenantName' => ['description' => 'Organization name', 'sensitive' => false, 'sample' => 'Acme Corp'],
            ],
        ],
        'password_reset' => [
            'name' => 'Password Reset',
            'description' => 'Sent when a user requests a password reset',
            'variables' => [
                'link' => ['description' => 'Password reset link (expires in 60min)', 'sensitive' => true, 'sample' => 'https://example.com/reset/xyz789'],
                'tenantName' => ['description' => 'Organization name', 'sensitive' => false, 'sample' => 'Acme Corp'],
            ],
        ],
        'two_factor_challenge' => [
            'name' => 'Two-Factor Email Challenge',
            'description' => 'Sent when a user requests an email one-time code during two-factor challenge',
            'variables' => [
                'code' => ['description' => 'One-time 2FA code (6 digits)', 'sensitive' => true, 'sample' => '123456'],
                'tenantName' => ['description' => 'Organization name', 'sensitive' => false, 'sample' => 'Acme Corp'],
            ],
        ],
        'notification' => [
            'name' => 'Notification Email',
            'description' => 'Wraps in-app notifications sent via email channel',
            'variables' => [
                'title' => ['description' => 'Notification title', 'sensitive' => false, 'sample' => 'New assignment'],
                'body' => ['description' => 'Notification body text', 'sensitive' => false, 'sample' => 'You have been assigned a new task.'],
                'link' => ['description' => 'Link to the notification target', 'sensitive' => false, 'sample' => '/tasks/42'],
                'tenantName' => ['description' => 'Organization name', 'sensitive' => false, 'sample' => 'Acme Corp'],
            ],
        ],
    ];
}
