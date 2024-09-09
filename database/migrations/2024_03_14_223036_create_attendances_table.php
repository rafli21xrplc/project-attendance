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
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->nullable()->constrained('student')->onDelete('cascade');
            $table->timestamp('time');
            $table->integer('hours');
            $table->boolean('is_spesialDay')->default(false);
            $table->enum('status', ['alpha', 'present', 'sick', 'permission'])->default('present');
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
