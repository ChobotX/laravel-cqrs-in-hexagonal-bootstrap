<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('landlord')->statement('CREATE SCHEMA IF NOT EXISTS landlord');
    }

    public function down(): void
    {
        DB::connection('landlord')->statement('DROP SCHEMA IF EXISTS landlord CASCADE');
    }
};
