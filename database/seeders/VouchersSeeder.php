<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VouchersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Voucher::create([
            'code' => 'GIAM10',
            'name' => 'Giảm 10% toàn bộ sản phẩm',
            'status' => true,
            'quantity' => 100,
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_value' => 0,
            'max_discount_value' => 50000,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        Voucher::create([
            'code' => 'GIAM50K',
            'name' => 'Giảm 50.000đ cho đơn từ 300.000đ',
            'status' => true,
            'quantity' => 50,
            'discount_type' => 'amount',
            'discount_value' => 50000,
            'min_order_value' => 300000,
            'max_discount_value' => null,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(45),
        ]);

        Voucher::create([
    'code' => 'FREESHIP',
    'name' => 'Miễn phí vận chuyển',
    'status' => true,
    'quantity' => 200,
    'discount_type' => 'percent',
    'discount_value' => 100,
    'min_order_value' => 0,
    'max_discount_value' => 30000,
    'start_date' => Carbon::now(),
    'end_date' => Carbon::now()->addMonths(2),
]);
    
    }
}
