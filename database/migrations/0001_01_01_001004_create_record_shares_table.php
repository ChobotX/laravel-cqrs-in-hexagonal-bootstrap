<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('record_shares', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('grantee_user_id');
            $table->string('resource_type');
            $table->uuid('resource_id');
            $table->string('action');
            $table->uuid('grantor_user_id');
            $table->uuid('organization_id');
            $table->timestamps();

            $table->unique(['grantee_user_id', 'resource_type', 'resource_id', 'action']);
            $table->index(['grantee_user_id', 'resource_type', 'action']);
            $table->index(['resource_type', 'resource_id']);

            $table->foreign('grantee_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('grantor_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('record_shares');
    }
};
