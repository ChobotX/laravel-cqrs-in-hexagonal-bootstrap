<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_rotation_settings', function (Blueprint $table): void {
            $table->unsignedSmallInteger('id')->primary();
            $table->boolean('rotation_enabled')->default(false);
            $table->unsignedSmallInteger('max_age_days')->nullable();
            $table->unsignedTinyInteger('history_count')->default(5);
        });

        DB::connection('tenant')->table('password_rotation_settings')->insert([
            'id' => 1,
            'rotation_enabled' => false,
            'max_age_days' => null,
            'history_count' => 5,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('password_rotation_settings');
    }
};
