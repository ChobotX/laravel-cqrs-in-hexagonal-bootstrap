<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('audit_log', 'changes')) {
            Schema::table('audit_log', function (Blueprint $table): void {
                $table->jsonb('changes')->default('[]');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('audit_log', 'changes')) {
            Schema::table('audit_log', function (Blueprint $table): void {
                $table->dropColumn('changes');
            });
        }
    }
};
