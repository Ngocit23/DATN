<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
        [
            'name' => 'Nike Air Max',
            'slug' => 'nike-air-max',
            'brand_id' => 1,
            'category_id' => 1,
            'status' => true,
        ],
        [
            'name' => 'Adidas Ultraboost',
            'slug' => 'adidas-ultraboost',
            'brand_id' => 2,
            'category_id' => 1,
            'status' => true,
        ],
    ]);
    }
}
