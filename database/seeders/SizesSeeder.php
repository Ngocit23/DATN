<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Size::insert([
            ['name' => '38'],
            ['name' => '39'],
            ['name' => '40'],
            ['name' => 'M'],
            ['name' => 'L'],
        ]);
    }
}
