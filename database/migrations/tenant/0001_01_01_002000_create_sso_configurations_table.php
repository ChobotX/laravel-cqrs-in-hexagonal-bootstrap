<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sso_configurations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('provider_type', 32);
            $table->string('slug', 64);
            $table->string('display_name');
            $table->boolean('enabled')->default(false);
            $table->boolean('enforce')->default(false);
            $table->string('jit_mode', 32);
            $table->json('allowed_email_domains');
            $table->text('config');
            $table->unsignedInteger('lock_version')->default(1);
            $table->timestamps();

            $table->unique(['provider_type', 'slug']);
            $table->index('enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_configurations');
    }
};
