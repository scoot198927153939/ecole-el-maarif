<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'supervisor', 'teacher', 'guardian') NOT NULL DEFAULT 'teacher'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'supervisor', 'teacher') NOT NULL DEFAULT 'teacher'");
    }
};
