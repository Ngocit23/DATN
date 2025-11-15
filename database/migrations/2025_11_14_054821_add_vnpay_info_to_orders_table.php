<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Mã giao dịch VNPAY
            $table->string('vnpay_transaction_no', 50)->nullable()->after('total');
            
            // Ngân hàng thanh toán
            $table->string('vnpay_bank_code', 50)->nullable()->after('vnpay_transaction_no');
            
            // Ngày thanh toán (định dạng YmdHis)
            $table->string('vnpay_pay_date', 14)->nullable()->after('vnpay_bank_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'vnpay_transaction_no',
                'vnpay_bank_code',
                'vnpay_pay_date'
            ]);
        });
    }
};