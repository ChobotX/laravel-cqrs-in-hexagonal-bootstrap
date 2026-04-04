<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('definitions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('namespace');
            $table->string('slug');
            $table->string('name');
            $table->integer('lock_version')->default(1);
            $table->timestamps();

            $table->unique(['namespace', 'slug']);
            $table->index('namespace');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('definitions');
    }
};
