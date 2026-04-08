<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grid_presets', function (Blueprint $table): void {
            $table->string('scope', 20)->default('personal')->after('position');
            $table->uuid('team_id')->nullable()->after('scope');

            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->index(['scope', 'grid_name']);
        });
    }

    public function down(): void
    {
        Schema::table('grid_presets', function (Blueprint $table): void {
            $table->dropIndex(['scope', 'grid_name']);
            $table->dropForeign(['team_id']);
            $table->dropColumn(['scope', 'team_id']);
        });
    }
};
