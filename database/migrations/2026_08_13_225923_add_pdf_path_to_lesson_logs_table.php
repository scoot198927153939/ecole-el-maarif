<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('lesson_logs', 'pdf_path')) {
            Schema::table('lesson_logs', function (Blueprint $table) {
                $table->string('pdf_path')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('lesson_logs', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
};