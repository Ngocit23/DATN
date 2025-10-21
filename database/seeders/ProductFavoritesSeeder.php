<?php

namespace Database\Seeders;

use App\Models\ProductFavorite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductFavoritesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductFavorite::insert([
            ['user_id' => 2, 'product_id' => 1],
            ['user_id' => 2, 'product_id' => 2],
            ['user_id' => 1, 'product_id' => 1],
        ]);
    }
}
