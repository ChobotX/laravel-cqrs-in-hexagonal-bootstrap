<?php

declare(strict_types=1);

it('stores valid locale in session', function (): void {
    $this->post('/locale', ['locale' => 'cs'])
        ->assertRedirect();

    $this->assertSame('cs', session('locale'));
});

it('ignores invalid locale', function (): void {
    $this->post('/locale', ['locale' => 'fr'])
        ->assertRedirect();

    $this->assertNull(session('locale'));
});

it('ignores empty locale', function (): void {
    $this->post('/locale', ['locale' => ''])
        ->assertRedirect();

    $this->assertNull(session('locale'));
});
