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
        Schema::create('subtraction_time', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tanggal');
            $table->integer('start_time');
            $table->integer('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.`
     */
    public function down(): void
    {
        Schema::dropIfExists('subtraction_time');
    }
};
