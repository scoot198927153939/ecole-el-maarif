<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->decimal('refund_amount', 10, 2); // المبلغ المُرجَّع لولي الأمر
            $table->date('refund_date');
            $table->string('method'); // cash أو transfer
            $table->string('transfer_service')->nullable();
            $table->string('transfer_number')->nullable();
            $table->string('sender_account')->nullable();
            $table->string('receiver_account')->nullable();
            $table->string('transfer_photo_path')->nullable();
            $table->foreignId('money_source_id')->nullable()->constrained('money_sources')->nullOnDelete();
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_refunds');
    }
};