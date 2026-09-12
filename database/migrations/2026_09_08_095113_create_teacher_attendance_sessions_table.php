<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedTinyInteger('session_number'); // 1, 2, أو 3
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['teacher_id', 'date', 'session_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendance_sessions');
    }
};