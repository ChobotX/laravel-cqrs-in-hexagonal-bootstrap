<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table): void {
            $table->uuid('id');
            $table->string('type', 100);
            $table->string('locale', 10);
            $table->string('subject_template', 500);
            $table->text('body_template');
            $table->integer('lock_version')->default(1);
            $table->timestamps();

            $table->primary('id');
            $table->unique(['type', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
