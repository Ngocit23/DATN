<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BrandsSeeder::class,
            CategoriesSeeder::class,
            ProductsSeeder::class,
            SizesSeeder::class,
            ColorsSeeder::class,
            ProductVariantsSeeder::class,
            UsersSeeder::class,
            AddressUsersSeeder::class,
            VouchersSeeder::class,
            OrdersSeeder::class,
            ProductFavoritesSeeder::class,
            OrderDetailsSeeder::class,
            CommentsSeeder::class,


        ]);
    }
}
