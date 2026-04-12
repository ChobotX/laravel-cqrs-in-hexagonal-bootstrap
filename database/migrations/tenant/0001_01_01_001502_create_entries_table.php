<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('definition_id');
            $table->unsignedInteger('definition_version');
            $table->string('namespace');
            $table->string('title');
            $table->jsonb('data');
            $table->uuid('created_by_user_id');
            $table->integer('lock_version')->default(1);
            $table->timestamps();

            $table->index(['namespace', 'definition_id']);
            $table->index('definition_id');
            $table->index('title');
            $table->index('created_by_user_id');
            $table->foreign('definition_id')->references('id')->on('definitions');
            $table->foreign('created_by_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
