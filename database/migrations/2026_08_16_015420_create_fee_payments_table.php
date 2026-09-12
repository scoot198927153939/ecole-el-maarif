<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('method', ['cash', 'transfer']);

            // تفاصيل التحويل البنكي (تُملأ بس لو method = transfer)
            $table->string('transfer_service')->nullable();
            $table->string('transfer_number')->nullable();
            $table->string('sender_account')->nullable();
            $table->string('receiver_account')->nullable();
            $table->string('transfer_photo_path')->nullable();

            $table->foreignId('money_source_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};