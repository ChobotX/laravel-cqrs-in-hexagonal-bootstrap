<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table): void {
            $table->uuid('id');
            $table->string('template_type', 100);
            $table->string('locale', 10);
            $table->uuid('recipient_id');
            $table->string('recipient_email', 255);
            $table->string('rendered_subject', 500);
            $table->text('rendered_body_masked');
            $table->jsonb('variable_keys');
            $table->string('trace_id', 64)->nullable();
            $table->timestampTz('sent_at');

            $table->primary('id');
            $table->index(['recipient_id', 'sent_at']);
            $table->index(['template_type', 'sent_at']);
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
