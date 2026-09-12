<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assessments', 'term')) {
            Schema::table('assessments', function (Blueprint $table) {
                $table->unsignedTinyInteger('term')->default(1)->after('type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('term');
        });
    }
};