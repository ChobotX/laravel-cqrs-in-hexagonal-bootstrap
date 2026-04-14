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
            $table->boolean('email_two_factor_enabled')->default(false)->after('password_changed_at');
            $table->timestampTz('email_two_factor_confirmed_at')->nullable()->after('email_two_factor_enabled');
            $table->text('totp_secret')->nullable()->after('email_two_factor_confirmed_at');
            $table->timestampTz('totp_confirmed_at')->nullable()->after('totp_secret');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'email_two_factor_enabled',
                'email_two_factor_confirmed_at',
                'totp_secret',
                'totp_confirmed_at',
            ]);
        });
    }
};
