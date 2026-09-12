<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_attendance_sessions', function (Blueprint $table) {
            $table->string('status')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('teacher_attendance_sessions', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent'])->default('present')->change();
        });
    }
};