<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('money_transactions', function (Blueprint $table) {
            $table->string('category', 30)->nullable()->after('description');
        });

        $backfill = [
            'staff_advance' => DB::table('staff_advances')->whereNotNull('money_transaction_id')->pluck('money_transaction_id'),
            'partner_withdrawal' => DB::table('partner_withdrawals')->whereNotNull('money_transaction_id')->pluck('money_transaction_id'),
            'salary' => DB::table('payroll_payments')->whereNotNull('money_transaction_id')->pluck('money_transaction_id'),
        ];

        foreach ($backfill as $category => $ids) {
            DB::table('money_transactions')->whereIn('id', $ids)->update(['category' => $category]);
        }

        DB::table('money_transactions')
            ->where('direction', 'in')
            ->where('description', 'like', 'تسديد رسوم الطالب:%')
            ->update(['category' => 'fee_payment']);

        DB::table('money_transactions')
            ->where('direction', 'out')
            ->where('description', 'like', 'إرجاع مبلغ لطالب منسحب:%')
            ->update(['category' => 'refund']);
    }

    public function down(): void
    {
        Schema::table('money_transactions', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
