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
    Schema::table('invoices', function (Blueprint $table) {
        $table->enum('procurement_status', [
            'pending','ordered','delivered','completed'
        ])->default('pending');

        $table->boolean('stock_processed')->default(false);

        $table->string('payment_method')->nullable();
        $table->string('payment_reference')->nullable();
    });
}

public function down(): void
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn([
            'procurement_status',
            'stock_processed',
            'payment_method',
            'payment_reference',
        ]);
    });
}
};
