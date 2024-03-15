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
        Schema::create('teacher', function (Blueprint $table) {
            $table->uuid('id')->index()->primary();
            $table->integer('nip');
            $table->integer('nuptk');
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->longText('address');
            $table->string('born_at');
            $table->date('day_of_birth');
            $table->string('telp');
            $table->string('position');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher');
    }
};
