<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id');
            $table->uuid('recipient_id');
            $table->string('type', 100);
            $table->string('title', 255);
            $table->text('body');
            $table->string('level', 20);
            $table->string('link', 2048)->nullable();
            $table->string('channel', 20);
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->index(['recipient_id', 'is_read', 'created_at']);
            $table->index(['recipient_id', 'created_at']);
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
