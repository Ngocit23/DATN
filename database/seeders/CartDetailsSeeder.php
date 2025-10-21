<?php

namespace Database\Seeders;

use App\Models\CartDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         CartDetail::create([
            'user_id' => 2,
            'product_variant_id' => 2,
            'quantity' => 1,
            'subtotal' => 2800000,
        ]);
    }
}
