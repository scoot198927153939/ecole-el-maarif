<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('staff_type'); // 'teacher' أو 'staff_member'
            $table->unsignedBigInteger('staff_id'); // يشاور لـ teachers.id أو staff_members.id حسب staff_type
            $table->date('date');
            $table->decimal('hours', 4, 2)->nullable(); // عدد ساعات الحضور بذاك اليوم
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};