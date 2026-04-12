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
        Schema::create('tenant_preferences', function (Blueprint $table): void {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('display_timezone', 64)->nullable();
        });

        DB::table('tenant_preferences')->insert([
            'id' => 1,
            'display_timezone' => null,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_preferences');
    }
};
