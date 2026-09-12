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
        Schema::create('lesson_logs', function (Blueprint $table) {
            $table->id();
          $table->foreignId('class_subject_teacher_id')->constrained('class_subject_teacher')->cascadeOnDelete();
$table->string('title');
$table->text('topic');
$table->date('lesson_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_logs');
    }
};
