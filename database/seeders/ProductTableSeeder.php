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
                'stock' => 100.00,
                'status' => 1,
                'userId' => 1, // Asegúrate de que este ID de usuario exista
                'categoryId' => 1, // Asegúrate de que este ID de categoría exista
            ],
            [
                'name' => 'Product 2',
                'description' => 'Description of Product 2',
                'stock' => 200.50,
                'status' => 1,
                'userId' => 1, // Asegúrate de que este ID de usuario exista
                'categoryId' => 1, // Asegúrate de que este ID de categoría exista
            ],
            [
                'name' => 'Product 3',
                'description' => 'Description of Product 3',
                'stock' => 0,
                'status' => 0, // Producto inactivo
                'userId' => 2, // Asegúrate de que este ID de usuario exista
                'categoryId' => 2, // Asegúrate de que este ID de categoría exista
            ],
            [
                'name' => 'Product 4',
                'description' => 'Description of Product 4',
                'stock' => 150.75,
                'status' => 1,
                'userId' => 3, // Asegúrate de que este ID de usuario exista
                'categoryId' => 3, // Asegúrate de que este ID de categoría exista
            ],
            [
                'name' => 'Product 5',
                'description' => 'Description of Product 5',
                'stock' => 50.00,
                'status' => 1,
                'userId' => null, // Sin usuario asignado
                'categoryId' => 2, // Asegúrate de que este ID de categoría exista
            ],
        ];

        // Insertar datos
        DB::table('products')->insert($products);
    }
}
