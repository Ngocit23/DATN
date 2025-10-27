<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            ProductImage::insert([
                ['product_id' => 1, 'image_url' => 'giaynike1.jpg'],
                ['product_id' => 1, 'image_url' => 'giaynike1.2.jpg'],
                ['product_id' => 1, 'image_url' => 'giaynike1.3.jpg'],
                ['product_id' => 1, 'image_url' => 'giaynike1.4.jpg'],

            ]);

            ProductImage::insert([
                ['product_id' => 2, 'image_url' => 'giaynike2.jpg'],
                ['product_id' => 2, 'image_url' => 'giaynike2.2.jpg'],
                ['product_id' => 2, 'image_url' => 'giaynike2.3.jpg'],
                ['product_id' => 2, 'image_url' => 'giaynike2.4.jpg'],

            ]);
    }
}
