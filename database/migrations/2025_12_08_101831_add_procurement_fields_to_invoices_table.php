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
        // Only add columns if they don't exist
        if (!Schema::hasColumn('invoices', 'procurement_status')) {
            $table->enum('procurement_status', [
                'pending','ordered','delivered','completed'
            ])->default('pending');
        }

        if (!Schema::hasColumn('invoices', 'stock_processed')) {
            $table->boolean('stock_processed')->default(false);
        }

        // payment_method and payment_reference already exist, skip them
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
