<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['term', 'exam_type', 'max_score']);
            $table->foreignId('assessment_id')->after('subject_id')->constrained()->cascadeOnDelete();
            $table->text('teacher_note')->nullable()->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropColumn(['assessment_id', 'teacher_note']);
            $table->string('term')->nullable();
            $table->string('exam_type')->nullable();
            $table->decimal('max_score', 5, 2)->nullable();
        });
    }
};