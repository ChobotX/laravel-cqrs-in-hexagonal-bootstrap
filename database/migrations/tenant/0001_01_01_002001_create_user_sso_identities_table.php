<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sso_identities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('configuration_id');
            $table->string('subject');
            $table->string('email_at_link');
            $table->timestamp('linked_at');
            $table->unsignedInteger('lock_version')->default(1);

            $table->unique(['configuration_id', 'subject']);
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('configuration_id')->references('id')->on('sso_configurations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sso_identities');
    }
};
