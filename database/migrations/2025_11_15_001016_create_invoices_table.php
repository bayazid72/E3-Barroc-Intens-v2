<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('contract_id')->nullable()->constrained('contracts');
            $table->dateTime('invoice_date');
            $table->decimal('total_amount', 10, 2);
            $table->dateTime('paid_at')->nullable();
            $table->enum('type', ['invoice','quote'])->default('invoice');
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
    }
};
