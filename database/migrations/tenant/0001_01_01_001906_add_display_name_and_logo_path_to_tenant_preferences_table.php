<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_preferences', function (Blueprint $table): void {
            $table->string('display_name')->default('')->after('id');
            $table->string('logo_path')->nullable()->after('display_name');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_preferences', function (Blueprint $table): void {
            $table->dropColumn(['display_name', 'logo_path']);
        });
    }
};
