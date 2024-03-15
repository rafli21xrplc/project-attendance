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
        Schema::create('attendance', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->foreignUuid('student_id')->nullable()->constrained('student')->onDelete('cascade');
            $table->foreignUuid('course_id')->nullable()->constrained('course')->onDelete('cascade');
            $table->foreignUuid('classroom_id')->nullable()->constrained('class_room')->onDelete('cascade');
            $table->timestamp('time');
            $table->enum('status', ['alpha', 'Present', 'sick', 'Permission']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
