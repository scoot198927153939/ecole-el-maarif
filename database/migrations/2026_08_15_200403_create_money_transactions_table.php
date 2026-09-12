<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('money_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('money_source_id')->constrained()->cascadeOnDelete();
            $table->enum('direction', ['in', 'out']); // in = إيداع، out = سحب
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->date('transaction_date');
            $table->string('document_path')->nullable(); // صورة الوصل/الإثبات
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_transactions');
    }
};