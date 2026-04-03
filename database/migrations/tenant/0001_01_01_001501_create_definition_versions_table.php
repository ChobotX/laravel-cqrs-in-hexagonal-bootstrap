<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('definition_versions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('definition_id');
            $table->unsignedInteger('version');
            $table->jsonb('body');
            $table->string('status');
            $table->timestamps();

            $table->unique(['definition_id', 'version']);
            $table->index(['definition_id', 'status']);
            $table->foreign('definition_id')->references('id')->on('definitions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('definition_versions');
    }
};
