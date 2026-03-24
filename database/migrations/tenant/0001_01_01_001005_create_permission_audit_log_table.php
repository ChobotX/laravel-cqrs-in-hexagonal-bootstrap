<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_audit_log', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('actor_user_id')->nullable();
            $table->uuid('acting_as_user_id')->nullable();
            $table->uuid('target_user_id')->nullable();
            $table->string('action');
            $table->json('payload');
            $table->timestamp('created_at');

            $table->index(['target_user_id', 'created_at']);
            $table->index('created_at');

            $table->foreign('actor_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('acting_as_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_audit_log');
    }
};
