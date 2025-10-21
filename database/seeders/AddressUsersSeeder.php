<?php

namespace Database\Seeders;

use App\Models\AddressUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AddressUser::create([
            'user_id' => 2,
            'address' => 'Số 1, Đường A, Quận B',
            'default' => true,
        ]);
    }
}
