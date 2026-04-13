<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('notifications', 'lock_version')) {
            Schema::table('notifications', function (Blueprint $table): void {
                $table->integer('lock_version')->default(1);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'lock_version')) {
            Schema::table('notifications', function (Blueprint $table): void {
                $table->dropColumn('lock_version');
            });
        }
    }
};
