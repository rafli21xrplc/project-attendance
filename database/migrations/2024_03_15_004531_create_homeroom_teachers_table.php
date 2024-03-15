<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('homeroom_teacher', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->foreignUuid('teacher_id')->constrained('teacher')->onDelete('cascade');
            $table->foreignUuid('class_id')->constrained('class_room')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homeroom_teacher');
    }
};
