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
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->enum('delivery_status', ['not_delivered', 'partially_delivered', 'delivered'])->default('not_delivered')->after('price_snapshot');
            $table->date('delivery_date')->nullable()->after('delivery_status');
            $table->text('delivery_notes')->nullable()->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'delivery_date', 'delivery_notes']);
        });
    }
};
