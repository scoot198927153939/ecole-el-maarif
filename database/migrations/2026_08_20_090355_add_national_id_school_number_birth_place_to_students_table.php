<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('national_id')->nullable()->after('student_number');
            $table->string('school_number')->nullable()->after('national_id');
            $table->string('birth_place')->nullable()->after('birth_date');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['national_id', 'school_number', 'birth_place']);
        });
    }
};