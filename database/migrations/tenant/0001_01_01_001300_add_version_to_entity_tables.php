<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['users', 'teams', 'roles', 'labels', 'notifications', 'record_shares', 'user_permission_overrides'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->integer('version')->default(1);
            });
        }
    }

    public function down(): void
    {
        $tables = ['users', 'teams', 'roles', 'labels', 'notifications', 'record_shares', 'user_permission_overrides'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('version');
            });
        }
    }
};
