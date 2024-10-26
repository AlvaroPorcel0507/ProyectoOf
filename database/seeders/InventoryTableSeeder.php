<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [
            [
                'quantity' => 25.50,
                'measurementUnit' => 'Caja', // Unidad de medida
                'unitPrice' => 12.50,
                'userId' => 1, // ID de un usuario existente
                'productId' => 1, // ID de un producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quantity' => 60.00,
                'measurementUnit' => 'Carga', // Unidad de medida
                'unitPrice' => 18.75,
                'userId' => 2, // ID de un usuario existente
                'productId' => 2, // ID de un producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quantity' => 100.00,
                'measurementUnit' => 'Caja', // Unidad de medida
                'unitPrice' => 10.00,
                'userId' => 3, // ID de un usuario existente
                'productId' => 3, // ID de un producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quantity' => 50.75,
                'measurementUnit' => 'Carga', // Unidad de medida
                'unitPrice' => 20.00,
                'userId' => 1, // ID de un usuario existente
                'productId' => 4, // ID de un producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quantity' => 80.25,
                'measurementUnit' => 'Caja', // Unidad de medida
                'unitPrice' => 15.50,
                'userId' => 2, // ID de un usuario existente
                'productId' => 5, // ID de un producto existente
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('your_table')->insert($entries);
    }
}
