<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_advances', function (Blueprint $table) {
            $table->foreignId('money_transaction_id')->nullable()->after('recorded_by')
                ->constrained('money_transactions')->nullOnDelete();
        });

        Schema::table('partner_withdrawals', function (Blueprint $table) {
            $table->foreignId('money_transaction_id')->nullable()->after('recorded_by')
                ->constrained('money_transactions')->nullOnDelete();
        });

        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->string('staff_type');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('amount', 10, 2);
            $table->foreignId('money_transaction_id')->nullable()->constrained('money_transactions')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['staff_type', 'staff_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');

        Schema::table('partner_withdrawals', function (Blueprint $table) {
            $table->dropForeign(['money_transaction_id']);
            $table->dropColumn('money_transaction_id');
        });

        Schema::table('staff_advances', function (Blueprint $table) {
            $table->dropForeign(['money_transaction_id']);
            $table->dropColumn('money_transaction_id');
        });
    }
};
