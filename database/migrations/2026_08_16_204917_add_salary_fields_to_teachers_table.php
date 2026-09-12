<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('teachers', 'salary_type')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->enum('salary_type', ['hourly', 'fixed'])->default('fixed');
                $table->decimal('fixed_salary', 10, 2)->nullable();
                $table->decimal('hourly_rate', 10, 2)->nullable();
                $table->boolean('is_partner')->default(false);
                $table->enum('payment_basis', ['salary', 'partnership'])->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['salary_type', 'fixed_salary', 'hourly_rate', 'is_partner', 'payment_basis']);
        });
    }
};