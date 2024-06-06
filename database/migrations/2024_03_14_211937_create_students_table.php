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
        Schema::create('student', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('student_id');
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->date('day_of_birth');
            $table->string('telp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};
