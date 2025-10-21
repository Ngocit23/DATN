<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'user_id' => 1,
            'ten_nguoinhan' => 'Nguyễn Văn A',
            'sdt_nguoinhan' => '0912345678',
            'diachi_nguoinhan' => 'Hà Nội',
            'payment_method' => 'COD',
            'payment_status' => 'unpaid',
            'total' => 2500000,
            'voucher_id' => 1, 
            'status' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Order::create([
            'user_id' => 2,
            'ten_nguoinhan' => 'Trần Thị B',
            'sdt_nguoinhan' => '0987654321',
            'diachi_nguoinhan' => 'TP. Hồ Chí Minh',
            'payment_method' => 'Online',
            'payment_status' => 'paid',
            'total' => 1200000,
            'voucher_id' => null,
            'status' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Order::create([
            'user_id' => 2,
            'ten_nguoinhan' => 'Phạm Văn C',
            'sdt_nguoinhan' => '0909123123',
            'diachi_nguoinhan' => 'Đà Nẵng',
            'payment_method' => 'COD',
            'payment_status' => 'unpaid',
            'total' => 890000,
            'voucher_id' => 2,
            'status' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
