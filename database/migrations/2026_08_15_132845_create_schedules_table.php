<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week'); // 1 = الاثنين ... 6 = السبت
            $table->unsignedTinyInteger('session_number'); // 1، 2، أو 3
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('class_subject_teacher_id')->constrained('class_subject_teacher')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};