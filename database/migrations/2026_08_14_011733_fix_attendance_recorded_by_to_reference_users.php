<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
        });
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreign('recorded_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->foreign('recorded_by')->references('id')->on('teachers')->cascadeOnDelete();
        });
    }
};