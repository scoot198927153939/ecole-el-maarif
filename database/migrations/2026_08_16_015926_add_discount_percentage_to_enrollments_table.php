<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('enrollments', 'discount_percentage')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->decimal('discount_percentage', 5, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
        });
    }
};