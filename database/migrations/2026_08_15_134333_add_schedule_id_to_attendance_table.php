<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendance', 'schedule_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->foreignId('schedule_id')->nullable()->after('enrollment_id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn('schedule_id');
        });
    }
};