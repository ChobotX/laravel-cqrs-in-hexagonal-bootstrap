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
        Schema::create('two_factor_settings', function (Blueprint $table): void {
            $table->unsignedSmallInteger('id')->primary();
            $table->boolean('required_for_all_users')->default(false);
            $table->boolean('email_otp_enabled')->default(true);
            $table->boolean('totp_enabled')->default(true);
        });

        DB::connection('tenant')->table('two_factor_settings')->insert([
            'id' => 1,
            'required_for_all_users' => false,
            'email_otp_enabled' => true,
            'totp_enabled' => true,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('two_factor_settings');
    }
};
