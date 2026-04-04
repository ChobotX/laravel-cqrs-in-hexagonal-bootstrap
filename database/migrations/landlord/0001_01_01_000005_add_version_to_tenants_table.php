<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['tenants', 'tenant_domains'];

        foreach ($tables as $table) {
            Schema::connection('landlord')->table($table, function (Blueprint $blueprint): void {
                $blueprint->integer('lock_version')->default(1);
            });
        }
    }

    public function down(): void
    {
        $tables = ['tenants', 'tenant_domains'];

        foreach ($tables as $table) {
            Schema::connection('landlord')->table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('lock_version');
            });
        }
    }
};
