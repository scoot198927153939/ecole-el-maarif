<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('role', ['director', 'supervisor', 'accountant', 'cleaner', 'guard']);
            $table->string('phone')->nullable();
            $table->enum('salary_type', ['hourly', 'fixed'])->default('fixed');
            $table->decimal('fixed_salary', 10, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_members');
    }
};