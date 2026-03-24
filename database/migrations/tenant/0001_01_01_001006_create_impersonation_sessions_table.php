<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impersonation_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('impersonator_user_id');
            $table->uuid('impersonated_user_id');
            $table->string('token')->unique();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();

            $table->index('token');
            $table->index(['impersonator_user_id', 'ended_at']);

            $table->foreign('impersonator_user_id')->references('id')->on('users');
            $table->foreign('impersonated_user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impersonation_sessions');
    }
};
