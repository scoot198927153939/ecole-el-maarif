<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_advances', function (Blueprint $table) {
            $table->id();
            $table->string('staff_type'); // 'teacher' أو 'staff_member'
            $table->unsignedBigInteger('staff_id');
            $table->decimal('amount', 10, 2); // مبلغ السلفة الأصلي
            $table->date('date_given');
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('staff_advance_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_advance_id')->constrained('staff_advances')->cascadeOnDelete();
            $table->decimal('amount', 10, 2); // المبلغ المقتطع هذا الشهر
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['staff_advance_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_advance_deductions');
        Schema::dropIfExists('staff_advances');
    }
};