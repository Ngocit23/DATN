<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AddressUser;

class AddressUserSeeder extends Seeder
{
    public function run()
    {
        AddressUser::create([
            'user_id' => 1, // user_id của người dùng (thay đổi thành ID người dùng thực tế)
            'address' => '123 Main St, Hanoi, Hai Ba Trung, Phuong Mai',  // Gộp tất cả thông tin vào một trường 'address'
            'default' => true // Đặt là địa chỉ mặc định
        ]);
    }
}

