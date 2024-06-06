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
        Schema::table('class_room', function (Blueprint $table) {
            $table->foreignUuid('type_class_id')->constrained('type_class')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_room', function (Blueprint $table) {
            $table->dropForeign('type_class_id');
            $table->dropColumn('type_class_id');
        });
    }
};
