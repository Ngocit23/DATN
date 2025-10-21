<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            ['name' => 'Giày Nam', 'slug' => 'giay-nam'],
            ['name' => 'Giày Nữ', 'slug' => 'giay-nu'],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien'],
        ]);
    }
}
