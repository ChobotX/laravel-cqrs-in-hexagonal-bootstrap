<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('trace_id', 64);
            $table->uuid('user_id')->nullable();
            $table->uuid('impersonator_id')->nullable();
            $table->string('command', 255);
            $table->string('action_label', 100);
            $table->string('entity_type', 100)->nullable();
            $table->uuid('entity_id')->nullable();
            $table->jsonb('payload');
            $table->jsonb('changes')->default('[]');
            $table->string('status', 10);
            $table->string('ip_address', 45)->nullable();
            $table->timestampTz('occurred_at');

            $table->index('trace_id');
            $table->index(['entity_type', 'entity_id', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
