<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labels', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('namespace');
            $table->string('name');
            $table->timestamps();

            $table->unique(['namespace', 'name']);
            $table->index('namespace');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labels');
    }
};
