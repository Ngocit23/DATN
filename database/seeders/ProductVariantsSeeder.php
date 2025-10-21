<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductVariantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductVariant::create([
            'product_id' => 1,
            'size_id' => 1,   // size 38
            'color_id' => 1,  // màu Đen
            'quantity' => 20,
            'price' => 2500000,
            'price_sale' => 2300000,
        ]);

        ProductVariant::create([
            'product_id' => 2,
            'size_id' => 2,   // size 39
            'color_id' => 2,  // màu Trắng
            'quantity' => 10,
            'price' => 2800000,
        ]);
    }
}
