<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            $table->uuid('id');
            $table->uuid('parent_team_id')->nullable();
            $table->string('name');
            $table->string('slug', 63)->unique();
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->foreign('parent_team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
