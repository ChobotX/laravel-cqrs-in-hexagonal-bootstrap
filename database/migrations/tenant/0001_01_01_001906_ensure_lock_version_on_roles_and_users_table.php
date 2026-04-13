<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['roles', 'users'] as $table) {
            $this->ensureLockVersion($table);
        }
    }

    public function down(): void
    {
        foreach (['roles', 'users'] as $table) {
            $this->dropLockVersionIfPresent($table);
        }
    }

    /** @param  non-empty-string  $table */
    private function ensureLockVersion(string $table): void
    {
        if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'lock_version')) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->integer('lock_version')->default(1);
            });
        }
    }

    /** @param  non-empty-string  $table */
    private function dropLockVersionIfPresent(string $table): void
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table, 'lock_version')) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('lock_version');
            });
        }
    }
};
