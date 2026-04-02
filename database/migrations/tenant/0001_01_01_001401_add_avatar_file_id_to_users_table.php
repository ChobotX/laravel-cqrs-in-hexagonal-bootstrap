<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('avatar_file_id')->nullable()->after('email');
            $table->foreign('avatar_file_id')->references('id')->on('files')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['avatar_file_id']);
            $table->dropColumn('avatar_file_id');
        });
    }
};
