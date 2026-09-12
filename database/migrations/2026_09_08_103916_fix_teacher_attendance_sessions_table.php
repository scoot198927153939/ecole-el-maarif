<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_attendance_sessions', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent'])->default('present')->after('session_number');
            $table->renameColumn('start_time', 'actual_start');
            $table->renameColumn('end_time', 'actual_end');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_attendance_sessions', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->renameColumn('actual_start', 'start_time');
            $table->renameColumn('actual_end', 'end_time');
        });
    }
};