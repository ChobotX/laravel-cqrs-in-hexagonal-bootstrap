<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('label_assignments', function (Blueprint $table): void {
            $table->uuid('label_id');
            $table->uuid('labelable_id');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['label_id', 'labelable_id']);
            $table->index('labelable_id');
            $table->foreign('label_id')->references('id')->on('labels')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label_assignments');
    }
};
