<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table): void {
            $table->uuid('id');
            $table->string('namespace', 63);
            $table->string('original_name', 255);
            $table->string('storage_path', 500)->unique();
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('size_in_bytes');
            $table->unsignedInteger('version_number');
            $table->uuid('uploaded_by');
            $table->timestamp('uploaded_at');
            $table->integer('lock_version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->index(['namespace', 'original_name', 'version_number']);
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
