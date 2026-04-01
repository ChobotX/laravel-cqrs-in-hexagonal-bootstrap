<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Blade;

it('renders control button with title and aria-label', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440660', 'name' => 'SA', 'email' => 'cb-basic@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-control-button label="Close menu"><span>X</span></x-control-button>',
    );

    expect($rendered)
        ->toContain('type="button"')
        ->toContain('data-tooltip="Close menu"')
        ->toContain('aria-label="Close menu"')
        ->toContain('<span>X</span>');
});

it('merges custom attributes', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440661', 'name' => 'SA', 'email' => 'cb-attrs@test.com']);
    $this->actingAs($user);

    $rendered = Blade::render(
        '<x-control-button class="text-gray-400" data-sidebar-close label="Close"><span>X</span></x-control-button>',
    );

    expect($rendered)
        ->toContain('text-gray-400')
        ->toContain('data-sidebar-close');
});
