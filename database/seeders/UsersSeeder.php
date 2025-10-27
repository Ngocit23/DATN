<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Ngocaz22@'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Khách A',
            'email' => 'usera@example.com',
            'password' => Hash::make('123456'),
            'role' => 'user',
        ]);
        
    }
}
