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
        Schema::table('payment_installment', function (Blueprint $table) {
            $table->foreignUuid('type_payment_id')->constrained('type_payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_installment', function (Blueprint $table) {
            $table->dropForeign('type_payment_id');
            $table->dropColumn('type_payment_id');
        });
    }
};
