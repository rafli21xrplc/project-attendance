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
        Schema::create('teaching_hour', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->integer('teaching_hours_id');
            $table->foreignUuid('teacher_id')->constrained('teacher')->onDelete('cascade');
            $table->foreignUuid('student_id')->constrained('student')->onDelete('cascade');
            $table->foreignUuid('course_id')->constrained('course')->onDelete('cascade');
            $table->integer('hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_hour');
    }
};
