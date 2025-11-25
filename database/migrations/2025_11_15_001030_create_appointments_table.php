<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->enum('type', ['malfunction','routine','installation']);
            $table->longText('description')->nullable();
            $table->dateTime('date_planned')->nullable();
            $table->dateTime('date_added');
            $table->enum('status', ['open','planned','done','cancelled'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('appointments');
    }
};
