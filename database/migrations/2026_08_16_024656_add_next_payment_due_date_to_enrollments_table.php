<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('enrollments', 'next_payment_due_date')) {
            Schema::table('enrollments', function (Blueprint $table) {
                $table->date('next_payment_due_date')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('next_payment_due_date');
        });
    }
};