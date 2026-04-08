<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_presets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('grid_name', 100);
            $table->string('name', 100);
            $table->jsonb('filters')->default('[]');
            $table->jsonb('sorting')->default('[]');
            $table->string('search', 200)->default('');
            $table->boolean('is_default')->default(false);
            $table->smallInteger('position')->default(0);
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'grid_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_presets');
    }
};
