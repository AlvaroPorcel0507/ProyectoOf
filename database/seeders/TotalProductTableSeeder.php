<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TotalProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalProducts = [
            [
                'stock' => 150.50,
                'userId' => 1, // ID del usuario existente
                'productId' => 1, // ID del producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock' => 200.00,
                'userId' => 2, // ID del usuario existente
                'productId' => 2, // ID del producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock' => 75.25,
                'userId' => 3, // ID del usuario existente
                'productId' => 3, // ID del producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock' => 500.00,
                'userId' => 1, // ID del usuario existente
                'productId' => 4, // ID del producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock' => 350.75,
                'userId' => 2, // ID del usuario existente
                'productId' => 5, // ID del producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('total_products')->insert($totalProducts);
    }
}
