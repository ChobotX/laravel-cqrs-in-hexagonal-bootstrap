<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_mail_settings', function (Blueprint $table): void {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('provider', 32);
            $table->string('host', 255);
            $table->unsignedInteger('port');
            $table->string('username', 255)->nullable();
            $table->text('password')->nullable();
            $table->string('encryption', 8)->nullable();
            $table->string('from_address', 255);
            $table->string('from_name', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_mail_settings');
    }
};
