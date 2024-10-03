<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Papa',
                'description' => 'Description of Product 1',
                'unitPrice' => 25.00, // Añadido el campo price
                'stock' => 100.00,
                'measurementUnit'=>'Caja',
                'status' => 1,
                'userId' => 4, // Asegúrate de que este ID de usuario exista
                'categoryId' => 1, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 2',
                'description' => 'Description of Product 2',
                'unitPrice' => 50.75, // Añadido el campo price
                'stock' => 200.50,
                'measurementUnit'=>'Caja',
                'status' => 1,
                'userId' => 4, // Asegúrate de que este ID de usuario exista
                'categoryId' => 1, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 3',
                'description' => 'Description of Product 3',
                'unitPrice' => 0, // Añadido el campo price
                'stock' => 0,
                'measurementUnit'=>'Caja',
                'status' => 0, // Producto inactivo
                'userId' => 2, // Asegúrate de que este ID de usuario exista
                'categoryId' => 2, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 4',
                'description' => 'Description of Product 4',
                'unitPrice' => 100.50, // Añadido el campo price
                'stock' => 150.75,
                'measurementUnit'=>'Caja',
                'status' => 1,
                'userId' => 3, // Asegúrate de que este ID de usuario exista
                'categoryId' => 3, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Product 5',
                'description' => 'Description of Product 5',
                'unitPrice' => 20.00, // Añadido el campo price
                'stock' => 50.00,
                'measurementUnit'=>'Caja',
                'status' => 1,
                'userId' => 2, // Sin usuario asignado
                'categoryId' => 2, // Asegúrate de que este ID de categoría exista
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insertar datos
        DB::table('products')->insert($products);
    }
}
