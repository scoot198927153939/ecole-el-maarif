<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('money_transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::create('money_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('money_transaction_id')->constrained('money_transactions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 20);
            $table->string('description', 255);
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_transaction_logs');

        Schema::table('money_transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
